
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("ngo");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requestId = (int)($_POST["request_id"] ?? 0);
    $beneficiaryId = (int)($_POST["beneficiary_id"] ?? 0);
    $qty = filter_var($_POST["quantity"] ?? "", FILTER_VALIDATE_FLOAT);
    $notes = trim($_POST["notes"] ?? "");

    try {
        if ($requestId < 1 || $beneficiaryId < 1 || !$qty || $qty <= 0) {
            throw new Exception("Enter valid distribution details.");
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "SELECT r.*,d.expiry_date
             FROM ngo_requests r
             JOIN donations d ON d.donation_id=r.donation_id
             WHERE r.request_id=? AND r.ngo_id=?
             FOR UPDATE"
        );
        $stmt->execute([$requestId,$_SESSION["user_id"]]);
        $r = $stmt->fetch();

        if (!$r || !in_array($r["status"], ["accepted","completed"], true)) {
            throw new Exception("Request does not belong to your accepted food.");
        }

        $stmt = $pdo->prepare(
            "SELECT beneficiary_id FROM beneficiaries
             WHERE beneficiary_id=?"
        );
        $stmt->execute([$beneficiaryId]);
        if (!$stmt->fetchColumn()) {
            throw new Exception("Beneficiary not found.");
        }

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(collected_quantity),0)
             FROM collections
             WHERE request_id=? AND status='collected'"
        );
        $stmt->execute([$requestId]);
        $collected = (float)$stmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(quantity),0)
             FROM distributions WHERE request_id=?"
        );
        $stmt->execute([$requestId]);
        $distributed = (float)$stmt->fetchColumn();

        $remaining = $collected - $distributed;

        if ($qty > $remaining) {
            throw new Exception(
                "Distribution exceeds collected balance: ".$remaining
            );
        }

        $stmt = $pdo->prepare(
            "INSERT INTO distributions
             (request_id,beneficiary_id,quantity,notes)
             VALUES (?,?,?,?)"
        );
        $stmt->execute([
            $requestId,$beneficiaryId,$qty,$notes
        ]);

        $newTotal = $distributed + $qty;

        if ($newTotal >= (float)$r["requested_quantity"]) {
            $stmt = $pdo->prepare(
                "UPDATE ngo_requests SET status='completed'
                 WHERE request_id=?"
            );
            $stmt->execute([$requestId]);
        }

        $pdo->commit();
        $success = "Distribution saved successfully.";
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$stmt = $pdo->prepare(
    "SELECT r.request_id,r.requested_quantity,d.food_name,d.unit,
        COALESCE(c.total_collected,0) AS collected,
        COALESCE(x.total_distributed,0) AS distributed
     FROM ngo_requests r
     JOIN donations d ON d.donation_id=r.donation_id
     LEFT JOIN (
        SELECT request_id,SUM(collected_quantity) AS total_collected
        FROM collections WHERE status='collected'
        GROUP BY request_id
     ) c ON c.request_id=r.request_id
     LEFT JOIN (
        SELECT request_id,SUM(quantity) AS total_distributed
        FROM distributions GROUP BY request_id
     ) x ON x.request_id=r.request_id
     WHERE r.ngo_id=? AND r.status IN ('accepted','completed')
     ORDER BY r.request_date DESC"
);
$stmt->execute([$_SESSION["user_id"]]);
$requests = $stmt->fetchAll();

$beneficiaries = $pdo->query(
    "SELECT b.beneficiary_id,u.name
     FROM beneficiaries b
     JOIN users u ON u.user_id=b.user_id
     ORDER BY u.name"
)->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>Distribute Food</h2>
<?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= h($success) ?></p><?php endif; ?>

<div class="card">
    <form method="post">
        <label>Accepted food request</label>
        <select name="request_id" required>
            <option value="">Select food</option>
            <?php foreach ($requests as $r):
                $remaining = (float)$r["collected"]-(float)$r["distributed"];
                if ($remaining <= 0) continue;
            ?>
            <option value="<?= (int)$r["request_id"] ?>">
                <?= h($r["food_name"]) ?>
                — Balance <?= h($remaining) ?> <?= h($r["unit"]) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <label>Beneficiary</label>
        <select name="beneficiary_id" required>
            <option value="">Select beneficiary</option>
            <?php foreach ($beneficiaries as $b): ?>
            <option value="<?= (int)$b["beneficiary_id"] ?>">
                <?= h($b["name"]) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <label>Quantity</label>
        <input type="number" name="quantity"
               min="0.01" step="0.01" required>

        <label>Notes</label>
        <textarea name="notes"></textarea>

        <button type="submit">Save Distribution</button>
    </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>