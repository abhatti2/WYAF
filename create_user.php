<?php
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name)) {
        $error = 'Name is required.';
    } elseif (empty($email)) {
        $error = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (empty($role)) {
        $error = 'Role is required.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } elseif (empty($confirm_password)) {
        $error = 'Confirm Password is required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert the new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $role, $hashed_password]);

            // Set success message
            $success = 'User created successfully! Redirecting to the manage users page...';
            header("refresh:3;url=manage_users.php"); // Redirect after 3 seconds
        } catch (PDOException $e) {
            $error = "Error creating user: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <?php include 'header.php'; ?>
    <div class="container container-custom mt-4">
        <h1 class="text-center mb-4 text-custom">Create New User</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="create_user.php">
            <div class="form-group">
                <label for="name" class="text-custom">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email" class="text-custom">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="role" class="text-custom">Role</label>
                <select class="form-control" id="role" name="role">
                    <option value="">Select a role</option>
                    <option value="admin" <?php if (isset($role) && $role === 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="user" <?php if (isset($role) && $role === 'user') echo 'selected'; ?>>User</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password" class="text-custom">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="confirm_password" class="text-custom">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit" class="btn btn-custom">Create User</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
