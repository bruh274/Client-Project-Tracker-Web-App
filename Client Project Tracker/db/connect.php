<?php

declare(strict_types=1);

$dsn = 'mysql:host=127.0.0.1;dbname=project_tracker;charset=utf8mb4';
$user = 'root';       // change if needed
$pass = '';           // change if needed

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('DB connection failed.');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_login(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
