<?php
session_start();
include 'config.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the user's current information from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
    if ($name && $email) {
        // Update the user's information in the database
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user_id]);
        
        // Update password if provided
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
        }
        
        // Update session information
        $_SESSION['name'] = $name;
        
        header("Location: welcome.php?message=Profile updated successfully");
        exit;
    } else {
        $error = "Please provide valid name and email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
    <style>
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-light text-dark">
    <div class="container mt-5 profile-container">
        <h1 class="text-custom mb-4">Profile Settings</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="profile_settings.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password (leave blank to keep current)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-custom mb-3">Update Profile</button>
        </form>
        
        <div>
            <a href="welcome.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
