
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("donor");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requestId = (int)($_POST["request_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($requestId < 1 || !in_array($action, ["accept","reject"], true)) {
        $error = "Invalid request.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "SELECT r.request_id, r.status AS request_status,
                        r.donation_id, d.status AS donation_status,
                        d.donor_id, d.quantity, d.expiry_date
                 FROM ngo_requests r
                 JOIN donations d ON d.donation_id=r.donation_id
                 WHERE r.request_id=?
                 FOR UPDATE"
            );
            $stmt->execute([$requestId]);
            $r = $stmt->fetch();

            if (!$r || (int)$r["donor_id"] !== (int)$_SESSION["user_id"]
                || $r["request_status"] !== "pending"
                || $r["donation_status"] !== "requested") {
                throw new Exception("Request is no longer pending.");
            }

            if ($action === "accept") {
                if ($r["expiry_date"] < date("Y-m-d")) {
                    throw new Exception("Donation has expired.");
                }

                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM ngo_requests
                     WHERE donation_id=? AND status='accepted'"
                );
                $stmt->execute([$r["donation_id"]]);
                if ((int)$stmt->fetchColumn() > 0) {
                    throw new Exception("Another request is already accepted.");
                }

                $stmt = $pdo->prepare(
                    "UPDATE ngo_requests SET status='accepted'
                     WHERE request_id=?"
                );
                $stmt->execute([$requestId]);

                $stmt = $pdo->prepare(
                    "UPDATE donations SET status='accepted'
                     WHERE donation_id=?"
                );
                $stmt->execute([$r["donation_id"]]);
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE ngo_requests SET status='rejected'
                     WHERE request_id=?"
                );
                $stmt->execute([$requestId]);

                $stmt = $pdo->prepare(
                    "UPDATE donations SET status='available'
                     WHERE donation_id=?"
                );
                $stmt->execute([$r["donation_id"]]);
            }

            $pdo->commit();
            $success = "Request updated.";
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare(
    "SELECT r.request_id,r.requested_quantity,r.status,
            r.request_date,d.food_name,d.quantity,d.unit,
            u.name AS ngo_name
     FROM ngo_requests r
     JOIN donations d ON d.donation_id=r.donation_id
     JOIN users u ON u.user_id=r.ngo_id
     WHERE d.donor_id=?
     ORDER BY r.request_date DESC"
);
$stmt->execute([$_SESSION["user_id"]]);
$rows = $stmt->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>Manage NGO Requests</h2>
<div class="card">
    <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= h($success) ?></p><?php endif; ?>
    <table>
        <tr>
            <th>Food</th><th>NGO</th><th>Requested</th>
            <th>Status</th><th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= h($r["food_name"]) ?></td>
            <td><?= h($r["ngo_name"]) ?></td>
            <td><?= h($r["requested_quantity"]) ?></td>
            <td><?= h($r["status"]) ?></td>
            <td>
                <?php if ($r["status"] === "pending"): ?>
                <form method="post">
                    <input type="hidden" name="request_id"
                           value="<?= (int)$r["request_id"] ?>">
                    <button name="action" value="accept">Accept</button>
                    <button name="action" value="reject">Reject</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>