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

// Get the category ID from the URL
$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

if ($category_id) {
    // Fetch the category name
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();

    if ($category) {
        // Fetch all pages associated with the selected category
        $stmt = $pdo->prepare("SELECT id, title FROM pages WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $pages = $stmt->fetchAll();
    } else {
        echo "Category not found.";
        exit;
    }
} else {
    echo "Invalid category ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pages in <?php echo htmlspecialchars($category['name']); ?></title>
</head>
<body>
    <h1>Pages in <?php echo htmlspecialchars($category['name']); ?></h1>
    <ul>
        <?php foreach ($pages as $page): ?>
            <li><a href="view_page.php?id=<?php echo $page['id']; ?>"><?php echo htmlspecialchars($page['title']); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <a href="list_categories.php">Back to Categories</a>
</body>
</html>
