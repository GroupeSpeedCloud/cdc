<?php
class AuthService
{
    public function getGoogleAuthUrl(): string
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => GOOGLE_SCOPE,
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];

        return GOOGLE_AUTH_URL . '?' . http_build_query($params);
    }

    public function handleCallback(string $code, string $state): array
    {
        try {
            // Validate state
            if (!isset($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $state)) {
                return ['success' => false, 'error' => 'Session de connexion expirée. Veuillez réessayer.'];
            }
            unset($_SESSION['oauth_state']);

            // Exchange code for token
            $tokenData = $this->fetchToken($code);
            if (empty($tokenData['access_token'])) {
                return ['success' => false, 'error' => 'Impossible de valider la connexion Google.'];
            }

            // Get user info
            $userInfo = $this->fetchUserInfo($tokenData['access_token']);
            if (empty($userInfo['email'])) {
                return ['success' => false, 'error' => 'Impossible de récupérer votre profil Google.'];
            }

            // Validate email_verified
            if (empty($userInfo['email_verified'])) {
                return ['success' => false, 'error' => 'Accès réservé à la direction'];
            }

            // Validate domain
            $email = strtolower(trim($userInfo['email']));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'error' => 'Adresse email Google invalide.'];
            }

            $domain = substr(strrchr($email, '@') ?: '', 1);
            if ($domain !== ALLOWED_DOMAIN) {
                return ['success' => false, 'error' => 'Accès réservé à la direction'];
            }

            if (!$this->isAuthorizedEmail($email)) {
                return ['success' => false, 'error' => 'Accès réservé à la direction'];
            }

            // Upsert user in DB
            $user = $this->upsertUser($userInfo);
            if (empty($user['id'])) {
                return ['success' => false, 'error' => 'Erreur lors de la connexion. Veuillez réessayer.'];
            }

            // Set session
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'     => $user['id'],
                'email'  => $email,
                'name'   => $userInfo['name'] ?? $email,
                'avatar' => $userInfo['picture'] ?? '',
            ];

            return ['success' => true, 'user' => $_SESSION['user']];
        } catch (Throwable $e) {
            error_log('AuthService::handleCallback error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur lors de la connexion. Veuillez réessayer.'];
        }
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    private function fetchToken(string $code): array
    {
        $postData = http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $postData,
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents(GOOGLE_TOKEN_URL, false, $context);
        if ($response === false) {
            error_log('AuthService::fetchToken failed to contact Google token endpoint');
            return [];
        }
        $data = json_decode($response, true) ?? [];
        if (empty($data['access_token'])) {
            error_log('AuthService::fetchToken response error: ' . json_encode($data));
        }
        return $data;
    }

    private function fetchUserInfo(string $accessToken): array
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => "Authorization: Bearer $accessToken\r\n",
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents(GOOGLE_USERINFO_URL, false, $context);
        if ($response === false) {
            error_log('AuthService::fetchUserInfo failed to contact Google userinfo endpoint');
            return [];
        }
        $data = json_decode($response, true) ?? [];
        if (empty($data['email'])) {
            error_log('AuthService::fetchUserInfo response error: ' . json_encode($data));
        }
        return $data;
    }

    private function isAuthorizedEmail(string $email): bool
    {
        $authorizedUsers = array_filter(array_map(
            static fn(string $value): string => strtolower(trim($value)),
            explode(',', AUTHORIZED_USERS)
        ));

        if ($authorizedUsers) {
            return in_array($email, $authorizedUsers, true);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE LOWER(email) = ?');
        $stmt->execute([$email]);

        return (bool) $stmt->fetch();
    }

    private function upsertUser(array $info): array
    {
        try {
            $pdo = getDB();
            $email    = strtolower(trim($info['email']));
            $googleId = $info['sub'] ?? '';
            $name     = $info['name'] ?? $email;
            $avatar   = $info['picture'] ?? '';

            $stmt = $pdo->prepare('SELECT id, google_id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                if (empty($existing['google_id'])) {
                    $pdo->prepare(
                        'UPDATE users SET google_id=?, name=?, avatar=?, last_login=NOW() WHERE email=?'
                    )->execute([$googleId, $name, $avatar, $email]);
                } else {
                    $pdo->prepare(
                        'UPDATE users SET name=?, avatar=?, last_login=NOW() WHERE email=?'
                    )->execute([$name, $avatar, $email]);
                }
                return ['id' => $existing['id']];
            } else {
                $pdo->prepare(
                    'INSERT INTO users (google_id, email, name, avatar, created_at, last_login)
                     VALUES (?, ?, ?, ?, NOW(), NOW())'
                )->execute([$googleId, $email, $name, $avatar]);
                return ['id' => $pdo->lastInsertId()];
            }
        } catch (Exception $e) {
            error_log('AuthService::upsertUser error: ' . $e->getMessage());
            return ['id' => 0];
        }
    }
}
