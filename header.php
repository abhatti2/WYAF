<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My CMS</title>
    <style>
        /* Add some basic styling */
        body {
            font-family: Arial, sans-serif;
        }
        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="search-form">
        <form action="search.php" method="get">
            <input type="text" name="keyword" placeholder="Search..." required>
            <button type="submit">Search</button>
        </form>
    </div>
