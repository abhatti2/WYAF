<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container my-4">
        <h1 class="text-center mb-4">Welcome to the Winnipeg Youth Arts Foundation (WYAF) Website</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="navbar-nav">
                    <a class="nav-link" href="list_categories.php">List of Categories</a>
                    <a class="nav-link" href="list_pages.php">List of Pages</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a class="nav-link" href="admin.php">Admin Page</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php">Logout</a>
                </div>
            </nav>
        <?php else: ?>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
