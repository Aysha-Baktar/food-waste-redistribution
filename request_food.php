
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("ngo");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $donationId = (int)($_POST["donation_id"] ?? 0);
    $qty = filter_var($_POST["quantity"] ?? "", FILTER_VALIDATE_FLOAT);

    try {
        if ($donationId < 1 || !$qty || $qty <= 0) {
            throw new Exception("Enter a valid quantity.");
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "SELECT * FROM donations
             WHERE donation_id=? FOR UPDATE"
        );
        $stmt->execute([$donationId]);
        $d = $stmt->fetch();

        if (!$d || $d["status"] !== "available"
            || $d["expiry_date"] < date("Y-m-d")) {
            throw new Exception("Food is not available.");
        }

        if ($qty > (float)$d["quantity"]) {
            throw new Exception("Requested quantity exceeds available food.");
        }

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM ngo_requests
             WHERE donation_id=? AND status='pending'"
        );
        $stmt->execute([$donationId]);

        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("This donation already has a pending request.");
        }

        $stmt = $pdo->prepare(
            "INSERT INTO ngo_requests
             (donation_id,ngo_id,requested_quantity)
             VALUES (?,?,?)"
        );
        $stmt->execute([
            $donationId,
            $_SESSION["user_id"],
            $qty
        ]);

        $stmt = $pdo->prepare(
            "UPDATE donations SET status='requested'
             WHERE donation_id=?"
        );
        $stmt->execute([$donationId]);

        $pdo->commit();
        $success = "Request sent to donor.";
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$stmt = $pdo->query(
    "SELECT d.*,u.name AS donor_name
     FROM donations d
     JOIN users u ON u.user_id=d.donor_id
     WHERE d.status='available'
       AND d.expiry_date >= CURDATE()
     ORDER BY d.expiry_date ASC"
);
$rows = $stmt->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>Available Food</h2>
<?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= h($success) ?></p><?php endif; ?>

<div class="grid">
<?php foreach ($rows as $d): ?>
    <div class="card">
        <h3><?= h($d["food_name"]) ?></h3>
        <p><?= h($d["description"]) ?></p>
        <p>Donor: <?= h($d["donor_name"]) ?></p>
        <p>Quantity: <?= h($d["quantity"]) ?> <?= h($d["unit"]) ?></p>
        <p>Expiry: <?= h($d["expiry_date"]) ?></p>
        <form method="post">
            <input type="hidden" name="donation_id"
                   value="<?= (int)$d["donation_id"] ?>">
            <label>Request quantity</label>
            <input type="number" name="quantity"
                   min="0.01" max="<?= h($d["quantity"]) ?>"
                   step="0.01" required>
            <button type="submit">Request Food</button>
        </form>
    </div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>