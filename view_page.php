<?php
session_start();
include 'config.php';
include 'header.php'; // Include the header file

// // React

// header('Content-Type: application/json');

// $page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
// if (!$page_id) {
//     http_response_code(400);
//     echo json_encode(['error' => 'Invalid page ID.']);
//     exit;
// }

// $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
// $stmt->execute([$page_id]);
// $page = $stmt->fetch(PDO::FETCH_ASSOC);

// if (!$page) {
//     http_response_code(404);
//     echo json_encode(['error' => 'Page not found.']);
//     exit;
// }

// // Optionally, fetch associated image
// $image_stmt = $pdo->prepare("SELECT * FROM images WHERE page_id = ?");
// $image_stmt->execute([$page_id]);
// $image = $image_stmt->fetch(PDO::FETCH_ASSOC);
// if ($image) {
//     $page['image'] = $image['filepath'];
// }

// echo json_encode($page);

// //React

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    // Fetch the associated image
    $stmt = $pdo->prepare("SELECT * FROM images WHERE page_id = ?");
    $stmt->execute([$page_id]);
    $image = $stmt->fetch();

    // Handle comment submission
    $error = '';
    $comment_text = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $comment_text = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $captcha = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($captcha !== $_SESSION['captcha']) {
            $error = 'Incorrect CAPTCHA. Please try again.';
        } else {
            $user_id = $_SESSION['user_id'];

            if ($comment_text) {
                $stmt = $pdo->prepare("INSERT INTO comments (page_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$page_id, $user_id, $comment_text]);
                $comment_text = ''; // Reset the comment text
            }
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

    <?php if ($image): ?>
        <img src="<?php echo htmlspecialchars($image['filepath']); ?>" alt="<?php echo htmlspecialchars($image['filename']); ?>" style="max-width: 100%; height: auto;">
    <?php endif; ?>

    <h2>Comments</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" action="view_page.php?id=<?php echo $page_id; ?>">
            <label for="comment">Add a comment:</label><br>
            <textarea id="comment" name="comment" required><?php echo htmlspecialchars($comment_text); ?></textarea><br><br>

            <label for="captcha">Enter the CAPTCHA:</label><br>
            <img src="captcha.php" alt="CAPTCHA"><br><br>
            <input type="text" id="captcha" name="captcha" required><br><br>

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
