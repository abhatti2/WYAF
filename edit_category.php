<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_categories.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if ($name) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        header("Location: manage_categories.php");
        exit;
    } else {
        echo "Please provide a category name.";
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
}

if (!$category) {
    header("Location: manage_categories.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
</head>
<body>
    <h1>Edit Category</h1>
    <form method="POST" action="edit_category.php?id=<?php echo $id; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($category['description']); ?></textarea><br><br>

        <button type="submit">Update Category</button>
    </form>
</body>
</html>
