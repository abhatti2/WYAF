<?php
// Database configuration
$host = '127.0.0.1';
$db = 'cms_db';
$user = 'cms_user';
$pass = 'code';

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
