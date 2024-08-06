<?php
include 'config.php';
include 'header.php';

header('Content-Type: application/json');

// Fetch categories from the database
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return categories as JSON
echo json_encode($categories);
?>
