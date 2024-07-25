<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the page ID from the URL
$page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($page_id) {
    // Fetch the page from the database
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$page_id]);
    $page = $stmt->fetch();

    if (!$page) {
        echo "Page not found.";
        exit;
    }

    // Handle comment submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
        $user_id = $_SESSION['user_id'];

        if ($comment) {
            $stmt = $pdo->prepare("INSERT INTO comments (page_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$page_id, $user_id, $comment]);
        }
    }

    // Fetch comments for this page
    $stmt = $pdo->prepare("SELECT comments.*, users.name FROM comments JOIN users ON comments.user_id = users.id WHERE page_id = ? ORDER BY created_at DESC");
    $stmt->execute([$page_id]);
    $comments = $stmt->fetchAll();
} else {
    echo "Invalid page ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page['title']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($page['title']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($page['content'])); ?></p>

    <h2>Comments</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" action="view_page.php?id=<?php echo $page_id; ?>">
            <label for="comment">Add a comment:</label><br>
            <textarea id="comment" name="comment" required></textarea><br><br>
            <button type="submit">Submit Comment</button>
        </form>
    <?php else: ?>
        <p>You must be logged in to comment. <a href="login.php">Login here</a></p>
    <?php endif; ?>

    <?php if ($comments): ?>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li>
                    <strong><?php echo htmlspecialchars($comment['name']); ?>:</strong>
                    <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    <small><?php echo $comment['created_at']; ?></small>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a href="delete_comment.php?id=<?php echo $comment['id']; ?>&page_id=<?php echo $page_id; ?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No comments yet. Be the first to comment!</p>
    <?php endif; ?>

    <a href="list_pages.php">Back to list</a>
</body>
</html>
