<?php
// Load .env file
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);
            if (
                strlen($value) >= 2
                && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))
            ) {
                $value = substr($value, 1, -1);
            }
            if (!array_key_exists($key, $_ENV)) {
                putenv("$key=$value");
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Required environment variables
$required = [
    'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS',
    'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET',
    'DOLIBARR_URL', 'DOLIBARR_API_KEY', 'APP_URL',
];
foreach ($required as $var) {
    if (getenv($var) === false) {
        die("Missing required environment variable: $var");
    }
}

// App constants
define('APP_URL',              rtrim(getenv('APP_URL'), '/'));
define('DB_HOST',              getenv('DB_HOST'));
define('DB_NAME',              getenv('DB_NAME'));
define('DB_USER',              getenv('DB_USER'));
define('DB_PASS',              getenv('DB_PASS'));
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));
define('AUTHORIZED_USERS',     getenv('AUTHORIZED_USERS') ?: '');
define('DOLIBARR_URL',         rtrim(getenv('DOLIBARR_URL'), '/'));
define('DOLIBARR_API_KEY',     getenv('DOLIBARR_API_KEY'));

// Secure session settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_only_cookies', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// Regenerate session ID periodically
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Error display (disable on production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('Europe/Paris');
