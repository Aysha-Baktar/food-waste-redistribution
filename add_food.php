
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("donor");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $food = trim($_POST["food_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $quantity = filter_var(
        $_POST["quantity"] ?? "",
        FILTER_VALIDATE_FLOAT
    );
    $unit = trim($_POST["unit"] ?? "");
    $expiry = $_POST["expiry_date"] ?? "";

    if (
        $food === "" || !$quantity || $quantity <= 0 ||
        $unit === "" || $expiry === "" ||
        $expiry < date("Y-m-d")
    ) {
        $error = "Please enter valid food details and expiry date.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO donations
            (donor_id,food_name,description,quantity,unit,expiry_date)
            VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            $_SESSION["user_id"],
            $food, $description, $quantity, $unit, $expiry
        ]);
        $success = "Food donation added successfully.";
    }
}

require __DIR__ . "/../includes/header.php";
?>
<div class="card">
    <h2>Add Food Donation</h2>
    <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= h($success) ?></p><?php endif; ?>

    <form method="post">
        <label>Food name</label>
        <input name="food_name" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Quantity</label>
        <input type="number" name="quantity"
               min="0.01" step="0.01" required>

        <label>Unit</label>
        <select name="unit" required>
            <option value="">Select unit</option>
            <option value="kg">Kilogram</option>
            <option value="pack">Pack</option>
            <option value="portion">Portion</option>
            <option value="piece">Piece</option>
            <option value="liter">Liter</option>
        </select>

        <label>Expiry date</label>
        <input type="date" name="expiry_date"
               min="<?= date("Y-m-d") ?>" required>

        <button type="submit">Submit Donation</button>
    </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>