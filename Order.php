<?php
/**
 * Order.php
 *
 * Order model class with CRUD methods and input validation.
 * PHP 8, procedural or OOP style.
 * Uses PDO for database interactions.
 * Database credentials are expected to be set as environment variables:
 *   DB_HOST, DB_NAME, DB_USER, DB_PASS
 */

class Order
{
    private int $id;
    private string $customer_name;
    private string $customer_email;
    private int $car_id;
    private int $quantity;
    private float $total_price;
    private string $status;
    private string $created_at;
    private string $updated_at;

    /**
     * Get PDO connection instance.
     *
     * @return PDO
     * @throws PDOException
     */
    private static function getConnection(): PDO
    {
        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }

    /**
     * Validate order data.
     *
     * @param array $data
     * @return array|true  Returns true if valid, otherwise array of error messages.
     */
    public static function validateInput(array $data)
    {
        $errors = [];

        // Required fields
        $required = ['customer_name', 'customer_email', 'car_id', 'quantity', 'total_price', 'status'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        // Email validation
        if (isset($data['customer_email']) && !filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['customer_email'] = 'Invalid email format.';
        }

        // Numeric validation
        if (isset($data['car_id']) && !filter_var($data['car_id'], FILTER_VALIDATE_INT)) {
            $errors['car_id'] = 'Car ID must be an integer.';
        }
        if (isset($data['quantity']) && !filter_var($data['quantity'], FILTER_VALIDATE_INT)) {
            $errors['quantity'] = 'Quantity must be an integer.';
        }
        if (isset($data['total_price']) && !is_numeric($data['total_price'])) {
            $errors['total_price'] = 'Total price must be a number.';
        }

        // Status validation (example: pending, paid, shipped, cancelled)
        $allowed_status = ['pending', 'paid', 'shipped', 'cancelled'];
        if (isset($data['status']) && !in_array(strtolower($data['status']), $allowed_status, true)) {
            $errors['status'] = 'Status must be one of: ' . implode(', ', $allowed_status) . '.';
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Create a new order.
     *
     * @param array $data
     * @return int|false  Returns the new order ID on success, false on failure.
     */
    public static function create(array $data)
    {
        $validation = self::validateInput($data);
        if ($validation !== true) {
            throw new InvalidArgumentException(json_encode($validation));
        }

        try {
            $pdo = self::getConnection();
            $sql = "INSERT INTO orders (customer_name, customer_email, car_id, quantity, total_price, status, created_at, updated_at)
                    VALUES (:customer_name, :customer_email, :car_id, :quantity, :total_price, :status, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':customer_name'  => $data['customer_name'],
                ':customer_email' => $data['customer_email'],
                ':car_id'         => $data['car_id'],
                ':quantity'       => $data['quantity'],
                ':total_price'    => $data['total_price'],
                ':status'         => $data['status'],
            ]);
            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('Order create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Read an order by ID.
     *
     * @param int $id
     * @return array|null  Returns order data array or null if not found.
     */
    public static function read(int $id): ?array
    {
        try {
            $pdo = self::getConnection();
            $sql = "SELECT * FROM orders WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();
            return $order ?: null;
        } catch (PDOException $e) {
            error_log('Order read error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing order.
     *
     * @param int   $id
     * @param array $data
     * @return bool  Returns true on success, false on failure.
     */
    public static function update(int $id, array $data): bool
    {
        $validation = self::validateInput($data);
        if ($validation !== true) {
            throw new InvalidArgumentException(json_encode($validation));
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE orders SET
                        customer_name = :customer_name,
                        customer_email = :customer_email,
                        car_id = :car_id,
                        quantity = :quantity,
                        total_price = :total_price,
                        status = :status,
                        updated_at = NOW()
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':customer_name'  => $data['customer_name'],
                ':customer_email' => $data['customer_email'],
                ':car_id'         => $data['car_id'],
                ':quantity'       => $data['quantity'],
                ':total_price'    => $data['total_price'],
                ':status'         => $data['status'],
                ':id'             => $id,
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Order update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an order by ID.
     *
     * @param int $id
     * @return bool  Returns true on success, false on failure.
     */
    public static function delete(int $id): bool
    {
        try {
            $pdo = self::getConnection();
            $sql = "DELETE FROM orders WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Order delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List all orders with optional pagination.
     *
     * @param int $limit  Number of records per page.
     * @param int $offset Offset for pagination.
     * @return array  Array of orders.
     */
    public static function listAll(int $limit = 20, int $offset = 0): array
    {
        try {
            $pdo = self::getConnection();
            $sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Order listAll error: ' . $e->getMessage());
            return [];
        }
    }
}
?>