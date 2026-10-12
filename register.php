
<?php
require_once __DIR__ . "/config/database.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $role = $_POST["role"] ?? "";

    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8 ||
        !in_array($role, ["donor", "ngo", "beneficiary"], true)
    ) {
        $error = "Enter valid details. Password needs 8 characters.";
    } else {
        try {
            $pdo->beginTransaction();

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (name,email,password,phone,address,role)
                VALUES (?,?,?,?,?,?)"
            );
            $stmt->execute([
                $name, $email, $hash,
                $phone, $address, $role
            ]);

            $userId = (int)$pdo->lastInsertId();

            if ($role === "beneficiary") {
                $stmt = $pdo->prepare(
                    "INSERT INTO beneficiaries
                    (user_id, household_size)
                    VALUES (?, 1)"
                );
                $stmt->execute([$userId]);
            }

            $pdo->commit();
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Registration failed. Email may already exist.";
        }
    }
}

require __DIR__ . "/includes/header.php";
?>
<div class="card">
    <h2>Create Account</h2>
    <?php if ($error): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Name</label>
        <input name="name" required maxlength="100">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password (minimum 8 characters)</label>
        <input type="password" name="password"
               minlength="8" required>

        <label>Phone</label>
        <input name="phone">

        <label>Address</label>
        <textarea name="address"></textarea>

        <label>Account type</label>
        <select name="role" required>
            <option value="">Select role</option>
            <option value="donor">Donor</option>
            <option value="ngo">NGO</option>
            <option value="beneficiary">Beneficiary</option>
        </select>

        <button type="submit">Register</button>
    </form>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>