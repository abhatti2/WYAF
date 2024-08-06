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
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container mt-4">
        <h1 class="text-center mb-4 text-custom">Pages in <?php echo htmlspecialchars($category['name']); ?></h1>
        <?php if ($pages): ?>
            <ul class="list-group mb-4">
                <?php foreach ($pages as $page): ?>
                    <li class="list-group-item">
                        <a href="view_page.php?id=<?php echo $page['id']; ?>" class="text-decoration-none text-custom">
                            <?php echo htmlspecialchars($page['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No pages found in this category.</p>
        <?php endif; ?>
        <a href="list_categories.php" class="btn btn-secondary mt-4">Back to Categories</a>
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
