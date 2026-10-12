
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("ngo");

$stmt = $pdo->query(
    "SELECT COUNT(*) FROM donations
     WHERE status='available' AND expiry_date >= CURDATE()"
);
$available = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM ngo_requests WHERE ngo_id=?"
);
$stmt->execute([$_SESSION["user_id"]]);
$requests = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COALESCE(SUM(collected_quantity),0)
     FROM collections
     WHERE ngo_id=? AND status='collected'"
);
$stmt->execute([$_SESSION["user_id"]]);
$collected = $stmt->fetchColumn();

require __DIR__ . "/../includes/header.php";
?>
<h2>NGO Dashboard</h2>
<div class="grid">
    <div class="card"><h3>Available Food</h3><h2><?= $available ?></h2></div>
    <div class="card"><h3>My Requests</h3><h2><?= $requests ?></h2></div>
    <div class="card"><h3>Collected Quantity</h3><h2><?= h($collected) ?></h2></div>
</div>
<div class="card">
    <a class="btn" href="request_food.php">Browse / Request Food</a>
    <a class="btn" href="my_requests.php">My Requests</a>
    <a class="btn" href="collect_food.php">Collect Food</a>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>