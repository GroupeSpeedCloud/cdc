<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->queryOne(
            'SELECT * FROM users WHERE email = ?',
            [strtolower(trim($email))]
        );
    }

    public function findByGoogleId(string $googleId): ?array
    {
        return $this->queryOne(
            'SELECT * FROM users WHERE google_id = ?',
            [$googleId]
        );
    }

    public function updateLastLogin(int $id): void
    {
        $this->pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')
                  ->execute([$id]);
    }
}
