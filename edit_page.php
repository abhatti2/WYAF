<?php
// Start the session and include the database configuration file
session_start();
include 'config.php';
include 'header.php'; // Include the header file

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

$page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$page_id) {
    echo "Invalid page ID.";
    exit;
}

// Fetch the page data
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

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

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

    // Handle image deletion
    $delete_image = isset($_POST['delete_image']) ? true : false;

    if ($title && $content && $category_id) {
        // Prepare and execute the SQL statement to update the page in the database
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE pages SET title = ?, content = ?, category_id = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$title, $content, $category_id, $page_id]);

            // If a new image is uploaded, insert it into the database
            if ($image_filename && $image_filepath) {
                // If there's an existing image, delete it
                if ($image) {
                    unlink($image['filepath']);
                    $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
                    $stmt->execute([$image['id']]);
                }

                $stmt = $pdo->prepare("INSERT INTO images (page_id, filename, filepath, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$page_id, $image_filename, $image_filepath]);
            }

            // If delete image checkbox is checked, remove the image from the database and file system
            if ($delete_image && $image) {
                unlink($image['filepath']);
                $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
                $stmt->execute([$image['id']]);
            }

            $pdo->commit();
            header("Location: admin.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to update page: " . $e->getMessage();
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
    <title>Edit Page</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container mt-4">
        <h1 class="text-center mb-4 text-custom">Edit Page</h1>
        <form method="POST" action="edit_page.php?id=<?php echo $page_id; ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="form-group mb-3">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($page['title']); ?>" required>
                <div class="invalid-feedback">Please provide a title.</div>
            </div>

            <div class="form-group mb-3">
                <label for="content">Content:</label>
                <textarea id="content" name="content" class="form-control" required><?php echo htmlspecialchars($page['content']); ?></textarea>
                <div class="invalid-feedback">Please provide content.</div>
            </div>

            <div class="form-group mb-3">
                <label for="category">Category:</label>
                <select id="category" name="category_id" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $page['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a category.</div>
            </div>

            <?php if ($image): ?>
                <div class="form-group mb-3">
                    <label>Current Image:</label><br>
                    <img src="<?php echo htmlspecialchars($image['filepath']); ?>" alt="<?php echo htmlspecialchars($image['filename']); ?>" class="img-fluid mb-2"><br>
                    <div class="form-check">
                        <input type="checkbox" id="delete_image" name="delete_image" class="form-check-input">
                        <label for="delete_image" class="form-check-label">Delete this image</label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group mb-3">
                <label for="image">Upload New Image (optional):</label>
                <input type="file" id="image" name="image" class="form-control-file">
            </div>

            <button type="submit" class="btn btn-custom mt-3 mb-4">Update Page</button>
        </form>
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

    <?php include 'footer.php'; ?>
    
</body>
</html>
