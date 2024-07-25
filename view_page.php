<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the page ID from the URL
$page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($page_id) {
    // Fetch the page from the database
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$page_id]);
    $page = $stmt->fetch();
} else {
    echo "Invalid page ID.";
    exit;
}

if (!$page) {
    echo "Page not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page['title']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($page['title']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($page['content'])); ?></p>
    <a href="list_pages.php">Back to list</a>
</body>
</html>
