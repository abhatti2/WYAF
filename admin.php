<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Determine the sorting method
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'title';
$valid_sort_columns = ['title', 'created_at', 'updated_at'];

if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'title';
}

// Fetch all pages from the database sorted by the chosen column
$stmt = $pdo->prepare("SELECT * FROM pages ORDER BY $sort_by");
$stmt->execute();
$pages = $stmt->fetchAll();

// Handle page approval
if (isset($_GET['approve_id'])) {
    $approve_id = filter_input(INPUT_GET, 'approve_id', FILTER_VALIDATE_INT);
    if ($approve_id) {
        $stmt = $pdo->prepare("UPDATE pages SET approved = TRUE WHERE id = ?");
        $stmt->execute([$approve_id]);
        header("Location: admin.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
    <style>
        th {
            cursor: pointer;
        }
        th.sort-asc::after {
            content: " ↑";
        }
        th.sort-desc::after {
            content: " ↓";
        }
    </style>
</head>
<body class="bg-light text-dark">
    <?php include 'header.php'; ?> <!-- Include the header -->
    <div class="container mt-4">
        <h1 class="text-center mb-4 text-custom">Admin Page</h1>
        <div class="d-flex justify-content-end mb-3">
            <a href="create_page.php" class="btn btn-custom">Create New Page</a>
        </div>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th><a href="?sort_by=title" class="<?php echo $sort_by == 'title' ? 'sort-asc' : ''; ?>">Title</a></th>
                    <th><a href="?sort_by=created_at" class="<?php echo $sort_by == 'created_at' ? 'sort-asc' : ''; ?>">Created At</a></th>
                    <th><a href="?sort_by=updated_at" class="<?php echo $sort_by == 'updated_at' ? 'sort-asc' : ''; ?>">Updated At</a></th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                <tr>
                    <td><?php echo htmlspecialchars($page['title']); ?></td>
                    <td><?php echo $page['created_at']; ?></td>
                    <td><?php echo $page['updated_at']; ?></td>
                    <td><?php echo $page['approved'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <?php if (!$page['approved']): ?>
                        <a href="?approve_id=<?php echo $page['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                        <?php endif; ?>
                        <a href="edit_page.php?id=<?php echo $page['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_page.php?id=<?php echo $page['id']; ?>" onclick="return confirm('Are you sure you want to delete this page?');" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include 'footer.php'; ?>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
