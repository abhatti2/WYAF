<?php
session_start();
include 'config.php';
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$keyword = '';
$category_id = '';
$results = [];
$error = '';
$results_per_page = 5; // Change this value for testing
$page = 1; // Default to the first page

if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
    $page = (int) $_GET['page'];
}

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $keyword = '%' . $keyword . '%';

    $query = "SELECT * FROM pages WHERE (title LIKE ? OR content LIKE ?)";
    $params = [$keyword, $keyword];

    if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
        $query .= " AND category_id = ?";
        $params[] = $category_id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $total_results = $stmt->rowCount();

    $start = ($page - 1) * $results_per_page;
    $query .= " LIMIT $start, $results_per_page";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    $total_pages = ceil($total_results / $results_per_page);
} else {
    $error = 'Please enter a search keyword.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container mt-5">
        <h1 class="text-custom">Search Results</h1>
        <?php if ($error): ?>
            <p class="text-danger"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($results): ?>
            <ul class="list-group">
                <?php foreach ($results as $result): ?>
                    <li class="list-group-item">
                        <a href="view_page.php?id=<?php echo $result['id']; ?>"><?php echo htmlspecialchars($result['title']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Pagination Links -->
            <nav class="pagination-custom">
                <?php if ($total_pages > 1): ?>
                    <?php if ($page > 1): ?>
                        <a href="?keyword=<?php echo urlencode($_GET['keyword']); ?>&category_id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>">Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?keyword=<?php echo urlencode($_GET['keyword']); ?>&category_id=<?php echo $category_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?keyword=<?php echo urlencode($_GET['keyword']); ?>&category_id=<?php echo $category_id; ?>&page=<?php echo $page + 1; ?>">Next</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
        <?php else: ?>
            <?php if (!$error): ?>
                <p>No results found for '<?php echo htmlspecialchars($_GET['keyword']); ?>'.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
