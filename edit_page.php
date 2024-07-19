<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Check if the page ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current data of the page
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $page = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validate and sanitize input
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

        if ($title && $content && $category_id) {
            // Update the page in the database
            $stmt = $pdo->prepare("UPDATE pages SET title = ?, content = ?, category_id = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$title, $content, $category_id, $id]);

            // Redirect to the admin page after successful update
            header("Location: admin.php");
            exit;
        } else {
            echo "Invalid input. Please fill in all fields correctly.";
        }
    }
} else {
    echo "Page ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Page</title>
</head>
<body>
    <h1>Edit Page</h1>
    <form method="POST" action="edit_page.php?id=<?php echo $id; ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($page['title']); ?>" required><br><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($page['content']); ?></textarea><br><br>

        <label for="category">Category:</label>
        <select id="category" name="category_id">
            <?php
            // Fetch categories from the database
            $stmt = $pdo->query("SELECT id, name FROM categories");
            while ($row = $stmt->fetch()) {
                $selected = $row['id'] == $page['category_id'] ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Update Page</button>
    </form>
</body>
</html>
