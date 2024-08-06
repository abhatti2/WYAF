<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Footer</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <footer class="bg-dark text-white py-4 mt-4">
        <div class="container text-center">
            <ul class="list-inline">
                <li class="list-inline-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="list-inline-item"><a href="register.php" class="text-white">Register</a></li>
                <li class="list-inline-item"><a href="logout.php" class="text-white">Logout</a></li>
            </ul>
            <p>&copy; <?php echo date("Y"); ?> Winnipeg Youth Arts Foundation (WYAF). All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
