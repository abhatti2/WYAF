<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Check if the page ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the page from the database
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect to the admin page after successful deletion
    header("Location: admin.php");
    exit;
} else {
    echo "Page ID not provided.";
    exit;
}
?>
