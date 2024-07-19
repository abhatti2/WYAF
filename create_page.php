<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if ($title && $content && $category_id) {
        // Prepare and execute the SQL statement to insert the new page into the database
        $stmt = $pdo->prepare("INSERT INTO pages (title, content, category_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$title, $content, $category_id]);

        // Redirect to the admin page after successful insertion
        header("Location: admin.php");
        exit;
    } else {
        echo "Invalid input. Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Page</title>
</head>
<body>
    <h1>Create New Page</h1>
    <form method="POST" action="create_page.php">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br><br>

        <label for="category">Category:</label>
        <select id="category" name="category_id">
            <?php
            // Fetch categories from the database
            $stmt = $pdo->query("SELECT id, name FROM categories");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Create Page</button>
    </form>
</body>
</html>
