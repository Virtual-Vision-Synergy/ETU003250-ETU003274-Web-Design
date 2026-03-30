<?php
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'db';
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'newsdb';
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'newsuser';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'newspass';

        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données.');
        }
    }
    return $pdo;
}
