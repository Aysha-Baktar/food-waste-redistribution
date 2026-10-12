
<?php
require_once __DIR__ . "/config/database.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT user_id, name, email, password, role
         FROM users WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        $pages = [
            "donor" => "donor/dashboard.php",
            "ngo" => "ngo/dashboard.php",
            "beneficiary" => "beneficiary/dashboard.php"
        ];

        header("Location: " . $pages[$user["role"]]);
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

require __DIR__ . "/includes/header.php";
?>
<div class="card">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p>New user? <a href="register.php">Register</a></p>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>