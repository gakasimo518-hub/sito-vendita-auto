<?php
/**
 * AdminAuth.php
 *
 * Handles admin authentication, session management, and access control.
 * Uses PDO for secure database interactions and PHP 8 features.
 */

declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

class AdminAuth
{
    private PDO $pdo;
    private string $table = 'admins';

    public function __construct()
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $db   = getenv('DB_NAME') ?: 'auto_sales';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed']));
        }
    }

    /**
     * Attempts to log in an admin user.
     *
     * @param string $username
     * @param string $password
     * @return bool True on success, false on failure.
     */
    public function login(string $username, string $password): bool
    {
        $sql = "SELECT id, username, password_hash FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }

        return false;
    }

    /**
     * Logs out the current admin user.
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Checks if an admin is currently logged in.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    /**
     * Returns the logged-in admin's ID, or null if not logged in.
     *
     * @return int|null
     */
    public function getAdminId(): ?int
    {
        return $_SESSION['admin_id'] ?? null;
    }

    /**
     * Returns the logged-in admin's username, or null if not logged in.
     *
     * @return string|null
     */
    public function getAdminUsername(): ?string
    {
        return $_SESSION['admin_username'] ?? null;
    }

    /**
     * Enforces that the current request is authenticated.
     * If not authenticated, sends a 401 response and terminates execution.
     */
    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            die(json_encode(['error' => 'Unauthorized']));
        }
    }
}
?>