
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("beneficiary");

$stmt = $pdo->prepare(
    "SELECT beneficiary_id FROM beneficiaries WHERE user_id=?"
);
$stmt->execute([$_SESSION["user_id"]]);
$beneficiaryId = $stmt->fetchColumn();

if (!$beneficiaryId) {
    die("Beneficiary profile not found.");
}

$stmt = $pdo->prepare(
    "SELECT d.food_name,dist.quantity,d.unit,
            dist.distribution_date,dist.notes
     FROM distributions dist
     JOIN ngo_requests r ON r.request_id=dist.request_id
     JOIN donations d ON d.donation_id=r.donation_id
     WHERE dist.beneficiary_id=?
     ORDER BY dist.distribution_date DESC"
);
$stmt->execute([$beneficiaryId]);
$rows = $stmt->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>Beneficiary Dashboard</h2>
<div class="card">
    <h3>My Received Food</h3>
    <table>
        <tr>
            <th>Food</th><th>Quantity</th>
            <th>Date</th><th>Notes</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= h($r["food_name"]) ?></td>
            <td><?= h($r["quantity"]) ?> <?= h($r["unit"]) ?></td>
            <td><?= h($r["distribution_date"]) ?></td>
            <td><?= h($r["notes"]) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>