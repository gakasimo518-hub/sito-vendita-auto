<?php
declare(strict_types=1);

class Database
{
    /** @var PDO|null */
    private static ?PDO $pdo = null;

    // Configurazione del database
    private const DB_HOST    = 'localhost';
    private const DB_NAME    = 'sito_vendita_auto';
    private const DB_USER    = 'root';
    private const DB_PASS    = '';
    private const DB_CHARSET = 'utf8mb4';

    // Costruttore privato per impedire istanziazione
    private function __construct() {}

    /**
     * Restituisce una connessione PDO al database MySQL.
     *
     * @return PDO
     * @throws RuntimeException se la connessione fallisce
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::DB_HOST,
                self::DB_NAME,
                self::DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                // Log dell'errore per il debugging, ma non esporre dettagli all'utente
                error_log('Database connection error: ' . $e->getMessage());
                throw new RuntimeException('Impossibile connettersi al database.');
            }
        }

        return self::$pdo;
    }
}