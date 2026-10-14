<?php
require __DIR__ . "/includes/header.php";

$examples = [
    ["DDL / Constraints", "Create a safe demo table with a primary key, NOT NULL fields and a CHECK constraint.", "CREATE TABLE IF NOT EXISTS sql_query_demo (\n    demo_id INT AUTO_INCREMENT PRIMARY KEY,\n    item_name VARCHAR(100) NOT NULL,\n    quantity DECIMAL(10,2) NOT NULL,\n    item_status VARCHAR(20) NOT NULL DEFAULT 'active',\n    CONSTRAINT chk_demo_quantity CHECK (quantity > 0)\n);"],
    ["INSERT", "Add example rows to the separate SQL demonstration table.", "INSERT INTO sql_query_demo (demo_id, item_name, quantity, item_status)\nVALUES (1, 'Rice demo', 10.00, 'active'),\n       (2, 'Vegetable demo', 5.00, 'pending')\nON DUPLICATE KEY UPDATE item_name = VALUES(item_name),\n    quantity = VALUES(quantity), item_status = VALUES(item_status);"],
    ["UPDATE", "Update a row in the demo table only; do not change real donation records for this example.", "UPDATE sql_query_demo\nSET quantity = 12.00, item_status = 'updated'\nWHERE demo_id = 1;"],
    ["DELETE", "Delete only the sample row from the demo table.", "DELETE FROM sql_query_demo\nWHERE demo_id = 2;"],
    ["SELECT-Filter", "Find donations whose status is available.", "SELECT donation_id, food_name, quantity, unit, expiry_date, status\nFROM donations\nWHERE status = 'available';"],
    ["ORDER BY", "Sort donations by earliest expiry date first.", "SELECT donation_id, food_name, expiry_date\nFROM donations\nORDER BY expiry_date ASC, food_name ASC;"],
    ["LIKE", "Search users whose name contains the text NGO.", "SELECT user_id, name, email, role\nFROM users\nWHERE name LIKE '%NGO%';"],
    ["IN-BETWEEN", "Find available or accepted donations with expiry dates in the selected range.", "SELECT donation_id, food_name, quantity, expiry_date\nFROM donations\nWHERE status IN ('available', 'accepted')\n  AND expiry_date BETWEEN '2026-01-01' AND '2027-12-31';"],
    ["TOP-LIMIT", "Show the five most recently added donations. MySQL uses LIMIT instead of TOP.", "SELECT donation_id, food_name, quantity\nFROM donations\nORDER BY created_at DESC\nLIMIT 5;"],
    ["JOIN", "Show donations together with the donor account details.", "SELECT d.donation_id, d.food_name, d.quantity, d.unit,\n       u.name AS donor_name, u.email AS donor_email\nFROM donations AS d\nINNER JOIN users AS u ON u.user_id = d.donor_id;"],
    ["JOIN (multiple tables)", "Show NGO requests with the requested food and NGO name.", "SELECT r.request_id, d.food_name, u.name AS ngo_name,\n       r.requested_quantity, r.status, r.request_date\nFROM ngo_requests AS r\nINNER JOIN donations AS d ON d.donation_id = r.donation_id\nINNER JOIN users AS u ON u.user_id = r.ngo_id;"],
    ["Aggregate-GROUP BY", "Count donations and sum donated quantity for each status.", "SELECT status, COUNT(*) AS total_donations,\n       SUM(quantity) AS total_quantity\nFROM donations\nGROUP BY status;"],
    ["Aggregate-GROUP BY", "Calculate the number and total quantity of requests per NGO.", "SELECT u.name AS ngo_name, COUNT(r.request_id) AS request_count,\n       SUM(r.requested_quantity) AS total_requested\nFROM users AS u\nINNER JOIN ngo_requests AS r ON r.ngo_id = u.user_id\nWHERE u.role = 'ngo'\nGROUP BY u.user_id, u.name;"],
    ["Subquery", "Find donations whose quantity is greater than the average donation quantity.", "SELECT donation_id, food_name, quantity\nFROM donations\nWHERE quantity > (SELECT AVG(quantity) FROM donations);"],
    ["Other", "Display the tables available in the selected database.", "SHOW TABLES;"],
    ["Other", "Count the registered users.", "SELECT COUNT(*) AS total_users FROM users;"],
];
?>

<section class="card query-intro">
    <h1>SQL Query Examples</h1>
    <p>This reference covers every required query category for the FoodShare project. The examples use the tables in <code>food_waste_db</code>.</p>
    <p><strong>Before running:</strong> import <code>database/food_waste_db.sql</code> first. Then open <code>database/query_examples.sql</code> in phpMyAdmin or your MySQL client to run the examples.</p>
    <p class="query-warning">The UPDATE and DELETE examples change only the separate <code>sql_query_demo</code> sample table, not your real donations or users.</p>
    <a class="btn" href="database/query_examples.sql" download>Download SQL Examples</a>
</section>

<?php foreach ($examples as $index => $example): ?>
    <section class="card query-card">
        <div class="query-heading">
            <span class="query-number"><?= $index + 1 ?></span>
            <h2><?= htmlspecialchars($example[0], ENT_QUOTES, "UTF-8") ?></h2>
        </div>
        <p><?= htmlspecialchars($example[1], ENT_QUOTES, "UTF-8") ?></p>
        <pre><code><?= htmlspecialchars($example[2], ENT_QUOTES, "UTF-8") ?></code></pre>
    </section>
<?php endforeach; ?>

<?php require __DIR__ . "/includes/footer.php"; ?>
