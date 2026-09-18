<?php
/**
 * api.php
 *
 * REST API router for the car sales website.
 * Handles CRUD operations for cars, orders, and authentication.
 *
 * Requirements:
 *   - PHP 8.0+
 *   - MySQL database
 *   - Environment variables:
 *       DB_HOST, DB_NAME, DB_USER, DB_PASS
 *
 * Tables (SQL example):
 *   CREATE TABLE cars (
 *       id INT AUTO_INCREMENT PRIMARY KEY,
 *       make VARCHAR(100) NOT NULL,
 *       model VARCHAR(100) NOT NULL,
 *       year INT NOT NULL,
 *       price DECIMAL(10,2) NOT NULL,
 *       description TEXT,
 *       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 *   );
 *
 *   CREATE TABLE orders (
 *       id INT AUTO_INCREMENT PRIMARY KEY,
 *       car_id INT NOT NULL,
 *       customer_name VARCHAR(255) NOT NULL,
 *       customer_email VARCHAR(255) NOT NULL,
 *       customer_phone VARCHAR(20),
 *       order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *       FOREIGN KEY (car_id) REFERENCES cars(id)
 *   );
 *
 *   CREATE TABLE tokens (
 *       token CHAR(32) PRIMARY KEY,
 *       user_id INT NOT NULL,
 *       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 *   );
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

/**
 * Database connection using PDO.
 *
 * @return PDO
 */
function getDb(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'car_sales';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
    return $pdo;
}

/**
 * Send JSON response with HTTP status code.
 *
 * @param mixed $data
 * @param int   $status
 */
function sendResponse($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

/**
 * Validate and retrieve JSON payload.
 *
 * @return array
 */
function getJsonPayload(): array
{
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(['error' => 'Invalid JSON payload'], 400);
    }
    return $data ?? [];
}

/**
 * Retrieve Authorization header.
 *
 * @return string|null
 */
function getAuthToken(): ?string
{
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/**
 * Check if the request is authenticated.
 *
 * @return bool
 */
function isAuthenticated(): bool
{
    $token = getAuthToken();
    if (!$token) {
        return false;
    }
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM tokens WHERE token = :token');
    $stmt->execute(['token' => $token]);
    return $stmt->fetch() !== false;
}

/**
 * Require authentication for protected routes.
 */
function requireAuth(): void
{
    if (!isAuthenticated()) {
        sendResponse(['error' => 'Unauthorized'], 401);
    }
}

/**
 * Generate a random token.
 *
 * @return string
 */
function generateToken(): string
{
    return bin2hex(random_bytes(16));
}

/**
 * Handle login: returns a token on successful authentication.
 */
function handleLogin(): void
{
    $data = getJsonPayload();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // For demo purposes, we use a hardcoded user.
    // In production, validate against a users table.
    if ($username === 'admin' && $password === 'admin123') {
        $token = generateToken();
        $pdo   = getDb();
        $stmt  = $pdo->prepare('INSERT INTO tokens (token, user_id) VALUES (:token, :user_id)');
        $stmt->execute(['token' => $token, 'user_id' => 1]);

        sendResponse(['token' => $token], 200);
    } else {
        sendResponse(['error' => 'Invalid credentials'], 401);
    }
}

/**
 * CRUD operations for cars.
 */
function getCars(): void
{
    $pdo = getDb();
    $stmt = $pdo->query('SELECT * FROM cars ORDER BY created_at DESC');
    $cars = $stmt->fetchAll();
    sendResponse(['cars' => $cars], 200);
}

function getCar(int $id): void
{
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM cars WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $car = $stmt->fetch();
    if ($car) {
        sendResponse(['car' => $car], 200);
    } else {
        sendResponse(['error' => 'Car not found'], 404);
    }
}

function createCar(): void
{
    requireAuth();
    $data = getJsonPayload();

    // Validation
    $required = ['make', 'model', 'year', 'price'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendResponse(['error' => "Field '$field' is required"], 400);
        }
    }

    $pdo = getDb();
    $stmt = $pdo->prepare('INSERT INTO cars (make, model, year, price, description) VALUES (:make, :model, :year, :price, :description)');
    $stmt->execute([
        'make'        => $data['make'],
        'model'       => $data['model'],
        'year'        => (int)$data['year'],
        'price'       => (float)$data['price'],
        'description' => $data['description'] ?? null,
    ]);

    $id = (int)$pdo->lastInsertId();
    sendResponse(['message' => 'Car created', 'id' => $id], 201);
}

function updateCar(int $id): void
{
    requireAuth();
    $data = getJsonPayload();

    // Build dynamic query
    $fields = [];
    $params = ['id' => $id];
    foreach (['make', 'model', 'year', 'price', 'description'] as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = :$field";
            $params[$field] = $data[$field];
        }
    }

    if (empty($fields)) {
        sendResponse(['error' => 'No fields to update'], 400);
    }

    $pdo = getDb();
    $stmt = $pdo->prepare('UPDATE cars SET ' . implode(', ', $fields) . ' WHERE id = :id');
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        sendResponse(['error' => 'Car not found or no changes'], 404);
    } else {
        sendResponse(['message' => 'Car updated'], 200);
    }
}

function deleteCar(int $id): void
{
    requireAuth();
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM cars WHERE id = :id');
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        sendResponse(['error' => 'Car not found'], 404);
    } else {
        sendResponse(['message' => 'Car deleted'], 200);
    }
}

/**
 * Order handling.
 */
function createOrder(): void
{
    $data = getJsonPayload();

    // Validation
    $required = ['car_id', 'customer_name', 'customer_email'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendResponse(['error' => "Field '$field' is required"], 400);
        }
    }

    // Check if car exists
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id FROM cars WHERE id = :id');
    $stmt->execute(['id' => $data['car_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Car not found'], 404);
    }

    $stmt = $pdo->prepare('INSERT INTO orders (car_id, customer_name, customer_email, customer_phone) VALUES (:car_id, :name, :email, :phone)');
    $stmt->execute([
        'car_id' => $data['car_id'],
        'name'   => $data['customer_name'],
        'email'  => $data['customer_email'],
        'phone'  => $data['customer_phone'] ?? null,
    ]);

    $id = (int)$pdo->lastInsertId();
    sendResponse(['message' => 'Order created', 'id' => $id], 201);
}

function getOrders(): void
{
    requireAuth();
    $pdo = getDb();
    $stmt = $pdo->query('SELECT o.*, c.make, c.model FROM orders o JOIN cars c ON o.car_id = c.id ORDER BY o.order_date DESC');
    $orders = $stmt->fetchAll();
    sendResponse(['orders' => $orders], 200);
}

/**
 * Routing
 */
$method = $_SERVER['REQUEST_METHOD'];
$path   = trim($_SERVER['PATH_INFO'] ?? '', '/');
$segments = explode('/', $path);

switch ($segments[0]) {
    case 'login':
        if ($method === 'POST') {
            handleLogin();
        } else {
            sendResponse(['error' => 'Method not allowed'], 405);
        }
        break;

    case 'cars':
        if ($method === 'GET' && count($segments) === 1) {
            getCars();
        } elseif ($method === 'GET' && count($segments) === 2 && is_numeric($segments[1])) {
            getCar((int)$segments[1]);
        } elseif ($method === 'POST' && count($segments) === 1) {
            createCar();
        } elseif ($method === 'PUT' && count($segments) === 2 && is_numeric($segments[1])) {
            updateCar((int)$segments[1]);
        } elseif ($method === 'DELETE' && count($segments) === 2 && is_numeric($segments[1])) {
            deleteCar((int)$segments[1]);
        } else {
            sendResponse(['error' => 'Not found'], 404);
        }
        break;

    case 'orders':
        if ($method === 'POST' && count($segments) === 1) {
            createOrder();
        } elseif ($method === 'GET' && count($segments) === 1) {
            getOrders();
        } else {
            sendResponse(['error' => 'Not found'], 404);
        }
        break;

    default:
        sendResponse(['error' => 'Endpoint not found'], 404);
        break;
}
?>