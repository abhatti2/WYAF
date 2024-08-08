<?php
session_start();
include 'config.php';

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get the user ID from the query string
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$user_id) {
    echo "Invalid user ID.";
    exit;
}

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    if (!$username) {
        $errors[] = "Username is required.";
    }

    if (!$email) {
        $errors[] = "Email is required.";
    }

    if (!$role) {
        $errors[] = "Role is required.";
    }

    if ($password && $password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Update user details in the database
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $role, $user_id]);

        // Update password if provided
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
        }

        header("Location: manage_users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="styles.css" rel="stylesheet"> <!-- External CSS -->
</head>
<body class="bg-light text-dark">
    <div class="container mt-5">
        <h1 class="text-custom mb-4">Edit User</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="edit_user.php?id=<?php echo $user_id; ?>" class="needs-validation" novalidate>
            <div class="form-group mb-3">
                <label for="username" class="text-custom">Username:</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                <div class="invalid-feedback">Please provide a username.</div>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="text-custom">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <div class="invalid-feedback">Please provide an email address.</div>
            </div>
            <div class="form-group mb-3">
                <label for="role" class="text-custom">Role:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
                <div class="invalid-feedback">Please select a role.</div>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="text-custom">Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="confirm_password" class="text-custom">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
            </div>
            <button type="submit" class="btn btn-custom mt-3">Update User</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script src="node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
