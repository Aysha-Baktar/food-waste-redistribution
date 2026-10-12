
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("ngo");

$stmt = $pdo->prepare(
    "SELECT r.*,d.food_name,d.unit,d.expiry_date,
        COALESCE(c.total_collected,0) AS total_collected
     FROM ngo_requests r
     JOIN donations d ON d.donation_id=r.donation_id
     LEFT JOIN (
        SELECT request_id,SUM(collected_quantity) AS total_collected
        FROM collections
        WHERE status='collected'
        GROUP BY request_id
     ) c ON c.request_id=r.request_id
     WHERE r.ngo_id=?
     ORDER BY r.request_date DESC"
);
$stmt->execute([$_SESSION["user_id"]]);
$rows = $stmt->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>My Food Requests</h2>
<div class="card">
<table>
    <tr>
        <th>Food</th><th>Requested</th><th>Collected</th>
        <th>Expiry</th><th>Status</th>
    </tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= h($r["food_name"]) ?></td>
        <td><?= h($r["requested_quantity"]) ?> <?= h($r["unit"]) ?></td>
        <td><?= h($r["total_collected"]) ?></td>
        <td><?= h($r["expiry_date"]) ?></td>
        <td><?= h($r["status"]) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>