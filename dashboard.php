
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("donor");

$stmt = $pdo->prepare(
    "SELECT COUNT(*) AS total,
            SUM(status='available') AS available
     FROM donations WHERE donor_id=?"
);
$stmt->execute([$_SESSION["user_id"]]);
$stats = $stmt->fetch();

require __DIR__ . "/../includes/header.php";
?>
<h2>Donor Dashboard</h2>
<div class="grid">
    <div class="card">
        <h3>My Donations</h3>
        <h2><?= (int)$stats["total"] ?></h2>
    </div>
    <div class="card">
        <h3>Available Donations</h3>
        <h2><?= (int)$stats["available"] ?></h2>
    </div>
</div>
<div class="card">
    <a class="btn" href="add_food.php">Add Food</a>
    <a class="btn" href="my_donations.php">My Donations</a>
    <a class="btn" href="manage_requests.php">Manage Requests</a>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>