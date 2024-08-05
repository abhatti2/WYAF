<?php
session_start();
include 'config.php';
include 'header.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$keyword = '';
$results = [];

if (isset($_GET['keyword'])) {
    $keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $keyword = '%' . $keyword . '%';

    // Search for pages in the database
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE title LIKE ? OR content LIKE ?");
    $stmt->execute([$keyword, $keyword]);
    $results = $stmt->fetchAll();
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
    <?php if ($results): ?>
        <ul>
            <?php foreach ($results as $result): ?>
                <li>
                    <a href="view_page.php?id=<?php echo $result['id']; ?>"><?php echo htmlspecialchars($result['title']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No results found for '<?php echo htmlspecialchars($_GET['keyword']); ?>'.</p>
    <?php endif; ?>
</body>
</html>
