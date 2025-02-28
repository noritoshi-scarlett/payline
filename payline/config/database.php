<?php
declare(strict_types=1);

class PdoFactory
{
    public static function create(): PDO
    {
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_NAME') ?: $_ENV['MYSQL_DATABASE'];
        $username = getenv('DB_USER') ?: $_ENV['MYSQL_USER'];
        $password = getenv('DB_PASSWORD') ?: $_ENV['MYSQL_PASSWORD'];

        $dsn = "mysql:host=$host:$port;dbname=$dbName;charset=utf8mb4";

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}

