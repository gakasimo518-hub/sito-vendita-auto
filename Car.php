<?php
/**
 * Car.php
 *
 * Model class for car entities with CRUD operations and input validation.
 * Uses PDO for database interactions and environment variables for configuration.
 *
 * @author  Your Name
 * @version 1.0
 */

class Car
{
    // Database connection parameters (use environment variables)
    private const DB_HOST = 'DB_HOST';
    private const DB_NAME = 'DB_NAME';
    private const DB_USER = 'DB_USER';
    private const DB_PASS = 'DB_PASS';

    // Car properties
    public ?int $id = null;
    public string $make;
    public string $model;
    public int $year;
    public float $price;
    public string $description;
    public string $image_url;

    /**
     * Constructor
     *
     * @param array $data Optional associative array to initialize properties.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id          = $data['id'] ?? null;
            $this->make        = $data['make'] ?? '';
            $this->model       = $data['model'] ?? '';
            $this->year        = $data['year'] ?? 0;
            $this->price       = $data['price'] ?? 0.0;
            $this->description = $data['description'] ?? '';
            $this->image_url   = $data['image_url'] ?? '';
        }
    }

    /**
     * Get PDO connection instance.
     *
     * @return PDO
     * @throws RuntimeException if connection fails.
     */
    private static function getConnection(): PDO
    {
        static $pdo = null;

        if ($pdo === null) {
            $host = getenv(self::DB_HOST);
            $db   = getenv(self::DB_NAME);
            $user = getenv(self::DB_USER);
            $pass = getenv(self::DB_PASS);

            if (!$host || !$db || !$user) {
                throw new RuntimeException('Database configuration is incomplete.');
            }

            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

            try {
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return $pdo;
    }

    /**
     * Validate car data.
     *
     * @throws InvalidArgumentException if validation fails.
     */
    public function validate(): void
    {
        $this->validateMake($this->make);
        $this->validateModel($this->model);
        $this->validateYear($this->year);
        $this->validatePrice($this->price);
        $this->validateDescription($this->description);
        $this->validateImageUrl($this->image_url);
    }

    private function validateMake(string $make): void
    {
        if (empty($make) || strlen($make) > 50) {
            throw new InvalidArgumentException('Make must be a non-empty string up to 50 characters.');
        }
    }

    private function validateModel(string $model): void
    {
        if (empty($model) || strlen($model) > 50) {
            throw new InvalidArgumentException('Model must be a non-empty string up to 50 characters.');
        }
    }

    private function validateYear(int $year): void
    {
        $currentYear = (int)date('Y');
        if ($year < 1886 || $year > $currentYear + 1) { // first car invented in 1886
            throw new InvalidArgumentException('Year must be between 1886 and next year.');
        }
    }

    private function validatePrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price must be a non-negative number.');
        }
    }

    private function validateDescription(string $description): void
    {
        if (strlen($description) > 1000) {
            throw new InvalidArgumentException('Description cannot exceed 1000 characters.');
        }
    }

    private function validateImageUrl(string $url): void
    {
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Image URL is not a valid URL.');
        }
    }

    /**
     * Create a new car record in the database.
     *
     * @return int The ID of the newly created car.
     * @throws Exception on failure.
     */
    public function create(): int
    {
        $this->validate();

        $sql = "INSERT INTO cars (make, model, year, price, description, image_url)
                VALUES (:make, :model, :year, :price, :description, :image_url)";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue(':make', $this->make, PDO::PARAM_STR);
        $stmt->bindValue(':model', $this->model, PDO::PARAM_STR);
        $stmt->bindValue(':year', $this->year, PDO::PARAM_INT);
        $stmt->bindValue(':price', $this->price, PDO::PARAM_STR); // use string for decimal
        $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
        $stmt->bindValue(':image_url', $this->image_url, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $this->id = (int)self::getConnection()->lastInsertId();
            return $this->id;
        } catch (PDOException $e) {
            throw new Exception('Failed to create car: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve all cars from the database.
     *
     * @return Car[] Array of Car objects.
     * @throws Exception on failure.
     */
    public static function readAll(): array
    {
        $sql = "SELECT * FROM cars ORDER BY year DESC, make ASC";
        $stmt = self::getConnection()->prepare($sql);

        try {
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $cars = [];
            foreach ($rows as $row) {
                $cars[] = new self($row);
            }
            return $cars;
        } catch (PDOException $e) {
            throw new Exception('Failed to retrieve cars: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a single car by ID.
     *
     * @param int $id
     * @return Car|null
     * @throws Exception on failure.
     */
    public static function readById(int $id): ?Car
    {
        $sql = "SELECT * FROM cars WHERE id = :id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? new self($row) : null;
        } catch (PDOException $e) {
            throw new Exception('Failed to retrieve car: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing car record.
     *
     * @return bool True on success.
     * @throws Exception on failure.
     */
    public function update(): bool
    {
        if ($this->id === null) {
            throw new Exception('Cannot update a car without an ID.');
        }

        $this->validate();

        $sql = "UPDATE cars
                SET make = :make,
                    model = :model,
                    year = :year,
                    price = :price,
                    description = :description,
                    image_url = :image_url
                WHERE id = :id";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue(':make', $this->make, PDO::PARAM_STR);
        $stmt->bindValue(':model', $this->model, PDO::PARAM_STR);
        $stmt->bindValue(':year', $this->year, PDO::PARAM_INT);
        $stmt->bindValue(':price', $this->price, PDO::PARAM_STR);
        $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
        $stmt->bindValue(':image_url', $this->image_url, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception('Failed to update car: ' . $e->getMessage());
        }
    }

    /**
     * Delete a car record by ID.
     *
     * @param int $id
     * @return bool True on success.
     * @throws Exception on failure.
     */
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM cars WHERE id = :id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception('Failed to delete car: ' . $e->getMessage());
        }
    }
}
?>