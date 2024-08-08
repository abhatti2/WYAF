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
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">
    <div class="container mt-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-custom">List of Pages</h1>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="create_page.php" class="btn btn-custom">Create New Page</a>
            <?php endif; ?>
        </div>
        <ul class="list-group">
            <?php foreach ($pages as $page): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="view_page.php?id=<?php echo $page['id']; ?>" class="text-decoration-none text-custom">
                        <?php echo htmlspecialchars($page['title']); ?>
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <span>
                            <a href="edit_page.php?id=<?php echo $page['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="delete_page.php?id=<?php echo $page['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                        </span>
                    <?php endif; ?>
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
