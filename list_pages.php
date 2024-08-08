<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';
include 'header.php'; // Include header for consistent styling

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all approved pages from the database
$stmt = $pdo->prepare("SELECT id, title FROM pages WHERE approved = TRUE");
$stmt->execute();
$pages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Pages</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">
    <div class="container mt-4 flex-grow-1">
        <h1 class="text-custom">List of Pages</h1>
        <div class="d-flex justify-content-end mb-3">
            <a href="create_page.php" class="btn btn-custom">Create New Page</a>
        </div>
        <ul class="list-group">
            <?php foreach ($pages as $page): ?>
                <li class="list-group-item list-group-item-action">
                    <a href="view_page.php?id=<?php echo $page['id']; ?>" class="text-decoration-none text-custom">
                        <?php echo htmlspecialchars($page['title']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
