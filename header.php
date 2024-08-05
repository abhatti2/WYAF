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
    <title>My CMS</title>
    <!-- <style>
        /* Add some basic styling */
        body {
            font-family: Arial, sans-serif;
        }
        .search-form {
            margin-bottom: 20px;
        }
    </style> -->
</head>
<body>
    <div class="search-form">
        <form action="search.php" method="get">
            <input type="text" name="keyword" placeholder="Search...">
            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>
