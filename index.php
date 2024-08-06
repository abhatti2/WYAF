<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container-fluid hero">
        <div class="hero-overlay text-center">
            <h1 class="display-4">Welcome to the Winnipeg Youth Arts Foundation (WYAF) Website</h1>
            <p class="lead">Empowering youth through arts.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-lg btn-custom">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container mt-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="navbar-nav mx-auto">
                    <a class="nav-link nav-link-custom" href="list_categories.php">List of Categories</a>
                    <a class="nav-link nav-link-custom" href="list_pages.php">List of Pages</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a class="nav-link nav-link-custom" href="admin.php">Admin Page</a>
                    <?php endif; ?>
                    <a class="nav-link nav-link-custom" href="logout.php">Logout</a>
                </div>
            </nav>
        <?php endif; ?>
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
