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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/painting_505910.png" alt="WYAF Logo" style="height: 40px;">
                WYAF
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="list_categories.php">List of Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="list_pages.php">List of Pages</a>
                    </li>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="manage_categories.php">Manage Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="admin.php">Admin Page</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero text-white text-center py-5 mb-4">
        <div class="hero-overlay">
            <div class="container">
                <h1 class="font-weight-light">Welcome to the Winnipeg Youth Arts Foundation (WYAF)</h1>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <div class="container">
        <div class="row text-center">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 container-custom">
                        <div class="card-body">
                            <h4 class="card-title text-custom">Category Management</h4>
                            <p class="card-text text-custom">Easily manage art categories and ensure proper organization of content.</p>
                        </div>
                        <div class="card-footer">
                            <a href="list_categories.php" class="btn btn-custom">View Categories</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 container-custom">
                        <div class="card-body">
                            <h4 class="card-title text-custom">Page Management</h4>
                            <p class="card-text text-custom">Create and manage pages with ease using our intuitive interface.</p>
                        </div>
                        <div class="card-footer">
                            <a href="list_pages.php" class="btn btn-custom">View Pages</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 container-custom">
                        <div class="card-body">
                            <h4 class="card-title text-custom">Admin Tools</h4>
                            <p class="card-text text-custom">Access powerful tools for managing users, content, and site settings.</p>
                        </div>
                        <div class="card-footer">
                            <a href="admin.php" class="btn btn-custom">Go to Admin Page</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 container-custom">
                        <div class="card-body">
                            <h4 class="card-title text-custom">Artworks</h4>
                            <p class="card-text text-custom">Explore artworks created by Winnipeg's youth.</p>
                        </div>
                        <div class="card-footer">
                            <a href="list_pages_by_category.php?category_id=3" class="btn btn-custom">View Artworks</a> <!-- Replace 1 with the actual category ID for artworks -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 container-custom">
                        <div class="card-body">
                            <h4 class="card-title text-custom">Events</h4>
                            <p class="card-text text-custom">Stay updated on upcoming events and activities hosted by WYAF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="list_pages_by_category.php?category_id=4" class="btn btn-custom">View Events</a> <!-- Replace 2 with the actual category ID for events -->
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
