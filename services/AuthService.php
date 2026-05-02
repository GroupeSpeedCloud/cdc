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
        // Validate state
        if (!isset($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $state)) {
            return ['success' => false, 'error' => 'Invalid OAuth state.'];
        }
        unset($_SESSION['oauth_state']);

        // Exchange code for token
        $tokenData = $this->fetchToken($code);
        if (!isset($tokenData['access_token'])) {
            return ['success' => false, 'error' => 'Failed to obtain access token.'];
        }

        // Get user info
        $userInfo = $this->fetchUserInfo($tokenData['access_token']);
        if (empty($userInfo['email'])) {
            return ['success' => false, 'error' => 'Failed to obtain user info.'];
        }

        // Validate email_verified
        if (empty($userInfo['email_verified'])) {
            return ['success' => false, 'error' => 'Accès réservé à la direction'];
        }

        // Validate domain
        $email = strtolower(trim($userInfo['email']));
        $domain = substr($email, strpos($email, '@') + 1);
        if ($domain !== ALLOWED_DOMAIN) {
            return ['success' => false, 'error' => 'Accès réservé à la direction'];
        }

        // Validate whitelist
        $allowedUsers = array_map(
            fn($u) => strtolower(trim($u)),
            explode(',', AUTHORIZED_USERS)
        );
        if (!in_array($email, $allowedUsers, true)) {
            return ['success' => false, 'error' => 'Accès réservé à la direction'];
        }

        // Upsert user in DB
        $user = $this->upsertUser($userInfo);

        // Set session
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'     => $user['id'],
            'email'  => $email,
            'name'   => $userInfo['name'] ?? $email,
            'avatar' => $userInfo['picture'] ?? '',
        ];

        return ['success' => true, 'user' => $_SESSION['user']];
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
            ],
        ]);

        $response = @file_get_contents(GOOGLE_TOKEN_URL, false, $context);
        if ($response === false) {
            return [];
        }
        return json_decode($response, true) ?? [];
    }

    private function fetchUserInfo(string $accessToken): array
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => "Authorization: Bearer $accessToken\r\n",
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents(GOOGLE_USERINFO_URL, false, $context);
        if ($response === false) {
            return [];
        }
        return json_decode($response, true) ?? [];
    }

    private function upsertUser(array $info): array
    {
        try {
            $pdo = getDB();
            $email    = strtolower(trim($info['email']));
            $googleId = $info['sub'] ?? '';
            $name     = $info['name'] ?? $email;
            $avatar   = $info['picture'] ?? '';

            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                $pdo->prepare(
                    'UPDATE users SET google_id=?, name=?, avatar=?, last_login=NOW() WHERE email=?'
                )->execute([$googleId, $name, $avatar, $email]);
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
