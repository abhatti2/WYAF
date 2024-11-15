<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Details</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">
    <?php
    include 'header.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Validate and get the category ID
    $category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($category_id === false || $category_id <= 0) {
        header("Location: manage_categories.php?error=invalid_id");
        exit;
    }

    // Fetch the category details
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();

    if (!$category) {
        header("Location: manage_categories.php?error=category_not_found");
        exit;
    }

    // Fetch pages associated with the category
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $pages = $stmt->fetchAll();
    ?>

    <div class="container mt-5 flex-grow-1">
        <h1 class="text-custom"><?php echo htmlspecialchars($category['name']); ?></h1>
        <p class="text-custom"><?php echo htmlspecialchars($category['description']); ?></p>

        <h2 class="text-custom mt-4">Pages in the <?php echo htmlspecialchars($category['name']); ?> Category</h2>
        <?php if (count($pages) > 0): ?>
        <ul class="list-group">
            <?php foreach ($pages as $page): ?>
            <li class="list-group-item">
                <a href="view_page.php?id=<?php echo htmlspecialchars($page['id']); ?>"><?php echo htmlspecialchars($page['title']); ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p>No pages found in the <?php echo htmlspecialchars($category['name']); ?> category.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
