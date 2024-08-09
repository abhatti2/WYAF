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

// Handle the form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

    if (!$title) {
        $errors[] = "Title is required.";
    }

    if (!$content) {
        $errors[] = "Content is required.";
    }

    if (!$category_id) {
        $errors[] = "Category is required.";
    }

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
            $errors[] = "Uploaded file is not a valid image.";
        } else {
            // Resize the image
            $resized_image_path = 'uploads/resized_' . $image_filename;
            if (!resizeImage($image_tmp_path, $resized_image_path, 800, 800)) {
                $errors[] = "Failed to resize image.";
            } else {
                // Move the resized image to the designated directory
                if (!rename($resized_image_path, $image_filepath)) {
                    $errors[] = "Failed to move uploaded file.";
                }
            }
        }
    }

    if (empty($errors)) {
        // Prepare and execute the SQL statement to insert the new page into the database
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO pages (title, content, category_id, user_id, created_at, updated_at, approved) VALUES (?, ?, ?, ?, NOW(), NOW(), FALSE)");
            $stmt->execute([$title, $content, $category_id, $user_id]);
            $page_id = $pdo->lastInsertId();

            if ($image_filename && $image_filepath) {
                $stmt = $pdo->prepare("INSERT INTO images (page_id, filename, filepath, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$page_id, $image_filename, $image_filepath]);
            }

            $pdo->commit();
            header("Location: list_pages.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to create page: " . $e->getMessage();
        }
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
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <?php include 'header.php' ?>
    <div class="container mt-5">
        <h1 class="text-custom mb-4">Create New Page</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="create_page.php" enctype="multipart/form-data" class="container-custom p-4 mt-4">
            <div class="form-group mb-3">
                <label for="title" class="text-custom">Title:</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($title ?? ''); ?>">
            </div>

            <div class="form-group mb-3">
                <label for="content" class="text-custom">Content:</label>
                <textarea id="content" name="content" class="form-control" rows="5"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="category" class="text-custom">Category:</label>
                <select id="category" name="category_id" class="form-control">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo isset($category_id) && $category_id == $category['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="image" class="text-custom">Image (optional):</label>
                <input type="file" id="image" name="image" class="form-control-file" accept="image/*">
            </div>

            <button type="submit" class="btn btn-custom mt-3">Create Page</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
