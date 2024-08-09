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
    <?php include 'header.php'; ?>


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

    <?php include 'footer.php'; ?>
    
    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

</body>
</html>
