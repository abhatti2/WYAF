<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the login success message, if any
$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

// Fetch some example metrics for the dashboard
$totalPages = 25; // Example: $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$totalCategories = 8; // Example: $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">
    <?php include 'header.php'; ?>
    <div class="container mt-5 flex-grow-1">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-custom">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                <p class="text-custom">You are logged in as <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>

                <?php if ($message): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-center">
                <img src="images/palette_12332575.png" alt="Profile Picture" class="img-fluid rounded-circle mb-2" style="max-width: 150px;">
                <p><a href="profile_settings.php" class="btn btn-success btn-sm">Profile Settings</a></p>
            </div>
        </div>

        <div class="row mt-4">
            <?php if ($_SESSION['role'] != 'admin'): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Home</h5>
                            <p class="card-text">View the home page.</p>
                            <a href="index.php" class="btn btn-custom">Go to Home</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Pages</h5>
                        <p class="card-text"><?php echo $totalPages; ?></p>
                        <a href="list_pages.php" class="btn btn-custom">View Pages</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Categories</h5>
                        <p class="card-text"><?php echo $totalCategories; ?></p>
                        <a href="list_categories.php" class="btn btn-custom">View Categories</a>
                    </div>
                </div>
            </div>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Admin Tools</h5>
                            <p class="card-text">Manage users, settings, and more.</p>
                            <a href="admin.php" class="btn btn-custom">Go to Admin Page</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 text-center">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
