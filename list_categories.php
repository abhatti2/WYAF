<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all categories from the database
$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Categories</title>
</head>
<body>
    <h1>List of Categories</h1>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><a href="list_pages_by_category.php?category_id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
