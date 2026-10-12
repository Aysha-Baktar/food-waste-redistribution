
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Food Waste Redistribution</title>
    <link rel="stylesheet"
          href="/food_waste/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="/food_waste/index.php">
        FoodShare
    </a>
    <nav>
        <?php if (isset($_SESSION["user_id"])): ?>
            <span>
                Hello, <?= htmlspecialchars(
                    $_SESSION["name"] ?? "User",
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </span>
            <a href="/food_waste/logout.php">Logout</a>
        <?php else: ?>
            <a href="/food_waste/login.php">Login</a>
            <a href="/food_waste/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">