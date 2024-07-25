<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get the comment ID and page ID from the URL
$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$page_id = filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT);

if ($comment_id && $page_id) {
    // Delete the comment from the database
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);

    // Redirect back to the page
    header("Location: view_page.php?id=$page_id");
    exit;
} else {
    echo "Invalid comment or page ID.";
    exit;
}
?>
