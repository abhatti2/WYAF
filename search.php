<?php
session_start();
include 'config.php';
include 'header.php'; // Include the header file

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$keyword = '';
$category_id = '';
$results = [];
$error = '';

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $keyword = '%' . $keyword . '%';

    if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

        // Search for pages in the specified category
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE (title LIKE ? OR content LIKE ?) AND category_id = ?");
        $stmt->execute([$keyword, $keyword, $category_id]);
    } else {
        // Search for pages in all categories
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE title LIKE ? OR content LIKE ?");
        $stmt->execute([$keyword, $keyword]);
    }

    $results = $stmt->fetchAll();
} else {
    $error = 'Please enter a search keyword.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
</head>
<body>
    <h1>Search Results</h1>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($results): ?>
        <ul>
            <?php foreach ($results as $result): ?>
                <li>
                    <a href="view_page.php?id=<?php echo $result['id']; ?>"><?php echo htmlspecialchars($result['title']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php if (!$error): ?>
            <p>No results found for '<?php echo htmlspecialchars($_GET['keyword']); ?>'.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
