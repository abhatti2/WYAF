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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
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
<body>
    <h1>Admin Page</h1>
    <a href="create_page.php">Create New Page</a>
    <table>
        <thead>
            <tr>
                <th><a href="?sort_by=title" class="<?php echo $sort_by == 'title' ? 'sort-asc' : ''; ?>">Title</a></th>
                <th><a href="?sort_by=created_at" class="<?php echo $sort_by == 'created_at' ? 'sort-asc' : ''; ?>">Created At</a></th>
                <th><a href="?sort_by=updated_at" class="<?php echo $sort_by == 'updated_at' ? 'sort-asc' : ''; ?>">Updated At</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
            <tr>
                <td><?php echo htmlspecialchars($page['title']); ?></td>
                <td><?php echo $page['created_at']; ?></td>
                <td><?php echo $page['updated_at']; ?></td>
                <td>
                    <a href="edit_page.php?id=<?php echo $page['id']; ?>">Edit</a>
                    <a href="delete_page.php?id=<?php echo $page['id']; ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
