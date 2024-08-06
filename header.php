<?php
include 'config.php';

// Fetch categories from the database
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WYAF</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">
    <header class="navbar-custom text-white py-3 mb-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- <h1 class="h3 font-weight-bold">WYAF</h1> -->
                <form action="search.php" method="get" class="form-inline w-100 max-w-75">
                    <div class="input-group w-100">
                        <input type="text" name="keyword" placeholder="Search..." class="form-control" aria-label="Search">
                        <select name="category_id" class="custom-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-custom">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </header>
    <div class="container mt-4">
        <!-- Add your other page content here -->
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
