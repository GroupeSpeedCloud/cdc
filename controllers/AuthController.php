<?php
class AuthController
{
    private AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function login(): void
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
        $error = $_GET['error'] ?? '';
        require_once __DIR__ . '/../views/login.php';
    }

    public function googleRedirect(): void
    {
        $url = $this->auth->getGoogleAuthUrl();
        header('Location: ' . $url);
        exit;
    }

    public function googleCallback(): void
    {
        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';
        $error = $_GET['error'] ?? '';

        if ($error) {
            header('Location: ' . APP_URL . '/login?error=' . urlencode('Connexion Google annulée.'));
            exit;
        }

        if (!$code || !$state) {
            header('Location: ' . APP_URL . '/login?error=' . urlencode('Paramètres manquants.'));
            exit;
        }

        $result = $this->auth->handleCallback($code, $state);

        if (!$result['success']) {
            header('Location: ' . APP_URL . '/login?error=' . urlencode($result['error']));
            exit;
        }

        header('Location: ' . APP_URL . '/');
        exit;
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}
