<?php
include 'config.php';

// Fetch categories for the search dropdown
$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll();
?>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #153448;">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="index.php">
            <img src="images/artistic_360090.png" alt="Logo" style="height: 40px; margin-right: 10px;">
            WYAF
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="list_pages.php">Pages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="list_categories.php">Categories</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin.php">Admin</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="profile_settings.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">Logout</a>
                </li>
            </ul>
            <form class="d-flex" action="search.php" method="GET">
                <input class="form-control me-2" type="search" name="keyword" placeholder="Search" aria-label="Search">
                <select class="form-select me-2" name="category_id">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-custom" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
