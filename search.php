<?php
session_start();
include 'config.php';
include 'header.php'; // Include the header file

// React
header('Content-Type: application/json');

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 5;
$start = ($page - 1) * $results_per_page;

if ($keyword === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Please enter a search keyword.']);
    exit;
}

$keyword = '%' . $keyword . '%';

$query = "SELECT * FROM pages WHERE (title LIKE ? OR content LIKE ?)";
$params = [$keyword, $keyword];

if ($category_id) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}

$total_query = $pdo->prepare($query);
$total_query->execute($params);
$total_results = $total_query->rowCount();

$query .= " LIMIT $start, $results_per_page";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_pages = ceil($total_results / $results_per_page);

$response = [
    'results' => $results,
    'total_pages' => $total_pages,
    'current_page' => $page,
];

echo json_encode($response);
// React

// // Enable error reporting for debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// $keyword = '';
// $category_id = '';
// $results = [];
// $error = '';
// $results_per_page = 5; // Default number of results per page, change this value for testing
// $page = 1; // Default to the first page

// if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
//     $page = (int) $_GET['page'];
// }

// if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
//     $keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
//     $keyword = '%' . $keyword . '%';

//     $query = "SELECT * FROM pages WHERE (title LIKE ? OR content LIKE ?)";
//     $params = [$keyword, $keyword];

//     if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
//         $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
//         $query .= " AND category_id = ?";
//         $params[] = $category_id;
//     }

//     // Get the total number of results
//     $stmt = $pdo->prepare($query);
//     $stmt->execute($params);
//     $total_results = $stmt->rowCount();

//     // Calculate the starting row for the results
//     $start = ($page - 1) * $results_per_page;

//     // Add LIMIT clause to the SQL query
//     $query .= " LIMIT $start, $results_per_page";

//     // Execute the query with the LIMIT clause
//     $stmt = $pdo->prepare($query);
//     $stmt->execute($params);
//     $results = $stmt->fetchAll();

//     // Calculate total pages
//     $total_pages = ceil($total_results / $results_per_page);
// } else {
//     $error = 'Please enter a search keyword.';
// }
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

        <!-- Pagination Links -->
        <div class="pagination">
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
        </div>
    <?php else: ?>
        <?php if (!$error): ?>
            <p>No results found for '<?php echo htmlspecialchars($_GET['keyword']); ?>'.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
