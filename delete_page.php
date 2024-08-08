<?php
session_start();
include 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Validate and get the page ID
$page_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($page_id <= 0) {
    echo "Invalid page ID.";
    exit;
}

// Delete the page
$stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
$stmt->execute([$page_id]);

header("Location: admin_approve_pages.php");
exit;
?>
