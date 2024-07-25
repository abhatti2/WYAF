<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
</head>
<body>
    <h1>Welcome to the Winnpeg Youth Arts Foundation (WYAF) Website</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <ul>
                <li><a href="list_categories.php">List of Categories</a></li>
                <li><a href="list_pages.php">List of Pages</a></li>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li><a href="admin.php">Admin Page</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</body>
</html>

