<?php

class Database extends PDO
{
    private static ?Database $instance = null;

    private function __construct()
    {
        $host = env('DB_HOST', 'localhost');
        $dbname = env('DB_NAME', 'maxitsa');
        $username = env('DB_USER', 'postgres');
        $password = env('DB_PASS', '');
        $port = env('DB_PORT', '5432');

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            parent::__construct($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction(): bool
    {
        return parent::beginTransaction();
    }

    public function commit(): bool
    {
        return parent::commit();
    }

    public function rollback(): bool
    {
        return parent::rollback();
    }

    public function executeTransaction(callable $callback): mixed
    {
        $this->beginTransaction();
        
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
