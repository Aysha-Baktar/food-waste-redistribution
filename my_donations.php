
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("donor");

$stmt = $pdo->prepare(
    "SELECT d.*,
       (SELECT COUNT(*) FROM ngo_requests r
        WHERE r.donation_id=d.donation_id) AS request_count
     FROM donations d
     WHERE d.donor_id=?
     ORDER BY d.created_at DESC"
);
$stmt->execute([$_SESSION["user_id"]]);
$rows = $stmt->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>My Donations</h2>
<div class="card">
    <a class="btn" href="add_food.php">Add Food</a>
    <p></p>
    <table>
        <tr>
            <th>Food</th><th>Quantity</th>
            <th>Expiry</th><th>Status</th><th>Requests</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= h($r["food_name"]) ?></td>
            <td><?= h($r["quantity"]) ?> <?= h($r["unit"]) ?></td>
            <td><?= h($r["expiry_date"]) ?></td>
            <td><?= h($r["status"]) ?></td>
            <td><?= (int)$r["request_count"] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>