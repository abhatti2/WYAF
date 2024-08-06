<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link href="output.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->

</head>
<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold text-center my-8">Welcome to the Winnipeg Youth Arts Foundation (WYAF) Website</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav class="bg-white shadow-md rounded-lg p-4 mb-8">
                <ul class="flex flex-col md:flex-row justify-center md:justify-start space-y-4 md:space-y-0 md:space-x-6">
                    <li><a href="list_categories.php" class="text-blue-500 hover:text-blue-700">List of Categories</a></li>
                    <li><a href="list_pages.php" class="text-blue-500 hover:text-blue-700">List of Pages</a></li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="admin.php" class="text-blue-500 hover:text-blue-700">Admin Page</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="text-blue-500 hover:text-blue-700">Logout</a></li>
                </ul>
            </nav>
        <?php else: ?>
            <div class="text-center">
                <a href="login.php" class="text-blue-500 hover:text-blue-700">Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
