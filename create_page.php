<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to resize image
function resizeImage($sourcePath, $destPath, $maxWidth, $maxHeight) {
    list($width, $height, $type) = getimagesize($sourcePath);

    if ($width <= $maxWidth && $height <= $maxHeight) {
        // No resizing needed, just copy the image
        return copy($sourcePath, $destPath);
    }

    $ratio = $width / $height;

    if ($width > $height) {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    } else {
        $newHeight = $maxHeight;
        $newWidth = $maxHeight * $ratio;
    }

    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $destPath, 90); // 90 is the quality
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $destPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $destPath);
            break;
    }

    imagedestroy($source);
    imagedestroy($newImage);

    return true;
}

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    $image_filename = null;
    $image_filepath = null;

    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_tmp_path = $_FILES['image']['tmp_name'];
        $image_filename = basename($_FILES['image']['name']);
        $image_filepath = 'uploads/' . $image_filename;

        // Check if the uploaded file is an image
        $image_info = getimagesize($image_tmp_path);
        if ($image_info === FALSE) {
            echo "Uploaded file is not a valid image.";
            exit;
        }

        // Resize the image
        $resized_image_path = 'uploads/resized_' . $image_filename;
        if (!resizeImage($image_tmp_path, $resized_image_path, 800, 800)) {
            echo "Failed to resize image.";
            exit;
        }

        // Move the resized image to the designated directory
        if (!rename($resized_image_path, $image_filepath)) {
            echo "Failed to move uploaded file.";
            exit;
        }
    }

    if ($title && $content && $category_id) {
        // Prepare and execute the SQL statement to insert the new page into the database
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO pages (title, content, category_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$title, $content, $category_id]);
            $page_id = $pdo->lastInsertId();

            if ($image_filename && $image_filepath) {
                $stmt = $pdo->prepare("INSERT INTO images (page_id, filename, filepath, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$page_id, $image_filename, $image_filepath]);
            }

            $pdo->commit();
            header("Location: admin.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to create page: " . $e->getMessage();
        }
    } else {
        echo "Invalid input. Please fill in all fields correctly.";
    }
}

// Fetch categories from the database
$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Page</title>
</head>
<body>
    <h1>Create New Page</h1>
    <form method="POST" action="create_page.php" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br><br>

        <label for="category">Category:</label>
        <select id="category" name="category_id" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="image">Image (optional):</label>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <button type="submit">Create Page</button>
    </form>
</body>
</html>
