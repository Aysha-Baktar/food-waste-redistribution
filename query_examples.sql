-- FoodShare SQL Query Examples
-- Database: food_waste_db (MySQL / MariaDB)
-- Run after importing database/food_waste_db.sql.
-- UPDATE and DELETE below affect ONLY the separate sql_query_demo table.

USE food_waste_db;

-- 1. DDL / CONSTRAINTS
-- A safe demonstration table for INSERT, UPDATE and DELETE examples.
CREATE TABLE IF NOT EXISTS sql_query_demo (
    demo_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    item_status VARCHAR(20) NOT NULL DEFAULT 'active',
    CONSTRAINT chk_demo_quantity CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. INSERT
INSERT INTO sql_query_demo (demo_id, item_name, quantity, item_status)
VALUES
    (1, 'Rice demo', 10.00, 'active'),
    (2, 'Vegetable demo', 5.00, 'pending')
ON DUPLICATE KEY UPDATE
    item_name = VALUES(item_name),
    quantity = VALUES(quantity),
    item_status = VALUES(item_status);

-- 3. UPDATE (safe: demonstration table only)
UPDATE sql_query_demo
SET quantity = 12.00, item_status = 'updated'
WHERE demo_id = 1;

-- 4. DELETE (safe: demonstration table only)
DELETE FROM sql_query_demo
WHERE demo_id = 2;

-- 5. SELECT-FILTER
SELECT donation_id, food_name, quantity, unit, expiry_date, status
FROM donations
WHERE status = 'available';

-- 6. ORDER BY
SELECT donation_id, food_name, expiry_date
FROM donations
ORDER BY expiry_date ASC, food_name ASC;

-- 7. LIKE
SELECT user_id, name, email, role
FROM users
WHERE name LIKE '%NGO%';

-- 8. IN-BETWEEN
SELECT donation_id, food_name, quantity, expiry_date
FROM donations
WHERE status IN ('available', 'accepted')
  AND expiry_date BETWEEN '2026-01-01' AND '2027-12-31';

-- 9. TOP-LIMIT (MySQL uses LIMIT instead of SQL Server TOP)
SELECT donation_id, food_name, quantity
FROM donations
ORDER BY created_at DESC
LIMIT 5;

-- 10. JOIN: donations with donor account
SELECT d.donation_id, d.food_name, d.quantity, d.unit,
       u.name AS donor_name, u.email AS donor_email
FROM donations AS d
INNER JOIN users AS u ON u.user_id = d.donor_id;

-- 10. JOIN: NGO requests with food and NGO account
SELECT r.request_id, d.food_name, u.name AS ngo_name,
       r.requested_quantity, r.status, r.request_date
FROM ngo_requests AS r
INNER JOIN donations AS d ON d.donation_id = r.donation_id
INNER JOIN users AS u ON u.user_id = r.ngo_id;

-- 11. AGGREGATE / GROUP BY: number of donations per status
SELECT status, COUNT(*) AS total_donations,
       SUM(quantity) AS total_quantity
FROM donations
GROUP BY status;

-- 11. AGGREGATE / GROUP BY: quantity requested by each NGO
SELECT u.name AS ngo_name, COUNT(r.request_id) AS request_count,
       SUM(r.requested_quantity) AS total_requested
FROM users AS u
INNER JOIN ngo_requests AS r ON r.ngo_id = u.user_id
WHERE u.role = 'ngo'
GROUP BY u.user_id, u.name;

-- 12. SUBQUERY: donations with quantity above the average
SELECT donation_id, food_name, quantity
FROM donations
WHERE quantity > (SELECT AVG(quantity) FROM donations);

-- 13. OTHER: inspect the tables in this database
SHOW TABLES;

-- 13. OTHER: count all registered users
SELECT COUNT(*) AS total_users FROM users;
