
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("ngo");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requestId = (int)($_POST["request_id"] ?? 0);
    $qty = filter_var($_POST["quantity"] ?? "", FILTER_VALIDATE_FLOAT);

    try {
        if ($requestId < 1 || !$qty || $qty <= 0) {
            throw new Exception("Enter a valid request and quantity.");
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "SELECT r.*,d.status AS donation_status,
                    d.expiry_date,d.donation_id
             FROM ngo_requests r
             JOIN donations d ON d.donation_id=r.donation_id
             WHERE r.request_id=? AND r.ngo_id=?
             FOR UPDATE"
        );
        $stmt->execute([$requestId,$_SESSION["user_id"]]);
        $r = $stmt->fetch();

        if (!$r || $r["status"] !== "accepted"
            || $r["donation_status"] !== "accepted"
            || $r["expiry_date"] < date("Y-m-d")) {
            throw new Exception("This accepted request cannot be collected.");
        }

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(collected_quantity),0)
             FROM collections
             WHERE request_id=? AND status='collected'"
        );
        $stmt->execute([$requestId]);
        $already = (float)$stmt->fetchColumn();

        $remaining = (float)$r["requested_quantity"] - $already;

        if ($qty > $remaining) {
            throw new Exception("Quantity exceeds remaining request amount: ".$remaining);
        }

        $stmt = $pdo->prepare(
            "INSERT INTO collections
             (donation_id,ngo_id,request_id,collected_quantity,status)
             VALUES (?,?,?,?,'collected')"
        );
        $stmt->execute([
            $r["donation_id"],
            $_SESSION["user_id"],
            $requestId,
            $qty
        ]);

        $pdo->commit();
        $success = "Collection recorded.";
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$stmt = $pdo->prepare(
    "SELECT r.request_id,r.requested_quantity,d.food_name,d.unit,
       COALESCE(c.total,0) AS collected
     FROM ngo_requests r
     JOIN donations d ON d.donation_id=r.donation_id
     LEFT JOIN (
       SELECT request_id,SUM(collected_quantity) AS total
       FROM collections WHERE status='collected'
       GROUP BY request_id
     ) c ON c.request_id=r.request_id
     WHERE r.ngo_id=? AND r.status='accepted'
       AND d.expiry_date >= CURDATE()
     ORDER BY r.request_date"
);
$stmt->execute([$_SESSION["user_id"]]);
$rows = $stmt->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>Collect Accepted Food</h2>
<?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= h($success) ?></p><?php endif; ?>

<?php foreach ($rows as $r):
    $remaining = (float)$r["requested_quantity"]-(float)$r["collected"];
    if ($remaining <= 0) continue;
?>
<div class="card">
    <h3><?= h($r["food_name"]) ?></h3>
    <p>Requested: <?= h($r["requested_quantity"]) ?> <?= h($r["unit"]) ?></p>
    <p>Collected: <?= h($r["collected"]) ?></p>
    <p>Remaining: <?= h($remaining) ?></p>
    <form method="post">
        <input type="hidden" name="request_id"
               value="<?= (int)$r["request_id"] ?>">
        <label>Collected quantity</label>
        <input type="number" name="quantity"
               min="0.01" max="<?= h($remaining) ?>"
               step="0.01" required>
        <button type="submit">Record Collection</button>
    </form>
</div>
<?php endforeach; ?>
<?php require __DIR__ . "/../includes/footer.php"; ?>