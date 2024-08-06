<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['logged_in' => false]);
    exit;
}

$response = [
    'logged_in' => true,
    'name' => htmlspecialchars($_SESSION['name']),
    'role' => htmlspecialchars($_SESSION['role']),
    'message' => isset($_GET['message']) ? htmlspecialchars($_GET['message']) : ''
];

echo json_encode($response);
?>
