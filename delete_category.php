<?php
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Check if 'id' is present in the URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Prepare the DELETE query and execute it
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect to the manage categories page after deletion
    header("Location: manage_categories.php");
    exit;
} else {
    // Redirect back to manage categories if the ID is invalid
    header("Location: manage_categories.php?error=invalid_id");
    exit;
}
?>
