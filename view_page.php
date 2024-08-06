<?php
session_start();
include 'config.php';
include 'header.php'; // Include the header file

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

        if (empty($comment_text)) {
            $error = 'Comment cannot be empty.';
        } elseif ($captcha !== $_SESSION['captcha']) {
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
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container mt-4">
        <h1 class="text-center mb-4 text-custom"><?php echo htmlspecialchars($page['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($page['content'])); ?></p>

        <?php if ($image): ?>
            <img src="<?php echo htmlspecialchars($image['filepath']); ?>" alt="<?php echo htmlspecialchars($image['filename']); ?>" class="img-fluid mb-4">
        <?php endif; ?>

        <h2 class="text-custom mb-4">Comments</h2>
        <?php if ($error): ?>
            <p class="text-danger mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="view_page.php?id=<?php echo $page_id; ?>" class="mb-5"> <!-- Increased bottom margin -->
                <div class="form-group mb-3">
                    <label for="comment">Add a comment:</label>
                    <textarea id="comment" name="comment" class="form-control"><?php echo htmlspecialchars($comment_text); ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="captcha">Enter the CAPTCHA:</label><br>
                    <img src="captcha.php" alt="CAPTCHA" class="mb-2"><br>
                    <input type="text" id="captcha" name="captcha" class="form-control">
                </div>

                <button type="submit" class="btn btn-custom mt-3">Submit Comment</button>
            </form>
        <?php else: ?>
            <p>You must be logged in to comment. <a href="login.php" class="text-custom">Login here</a></p>
        <?php endif; ?>

        <?php if ($comments): ?>
            <ul class="list-group mb-4">
                <?php foreach ($comments as $comment): ?>
                    <li class="list-group-item mb-2">
                        <strong><?php echo htmlspecialchars($comment['name']); ?>:</strong>
                        <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                        <small><?php echo $comment['created_at']; ?></small>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                            <a href="delete_comment.php?id=<?php echo $comment['id']; ?>&page_id=<?php echo $page_id; ?>" onclick="return confirm('Are you sure you want to delete this comment?');" class="text-danger">Delete</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>

        <a href="list_pages.php" class="btn btn-secondary mt-4 mb-5">Back to list</a> <!-- Added bottom margin -->
    </div>

    <?php include 'footer.php'; ?>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
