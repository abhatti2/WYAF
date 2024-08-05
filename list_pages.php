<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all pages from the database
$stmt = $pdo->query("SELECT id, title FROM pages");
$pages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Pages</title>
</head>
<body>
    <h1>List of Pages</h1>
    <ul>
        <?php foreach ($pages as $page): ?>
            <li><a href="view_page.php?id=<?php echo $page['id']; ?>"><?php echo htmlspecialchars($page['title']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
