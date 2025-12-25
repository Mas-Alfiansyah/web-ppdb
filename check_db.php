<?php
// check_db.php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

echo "Checking database schema...\n";

// Add seen_announcement column if not exists
try {
    $conn->query("ALTER TABLE siswa ADD COLUMN IF NOT EXISTS seen_announcement TINYINT DEFAULT 0");
    echo "Column 'seen_announcement' checked/added successfully.\n";
} catch (Exception $e) {
    echo "Error adding column: " . $e->getMessage() . "\n";
}

// Verify users_admin exists
$res = $conn->query("SHOW TABLES LIKE 'users_admin'");
if ($res->num_rows > 0) {
    echo "Table 'users_admin' exists.\n";
} else {
    echo "Table 'users_admin' MISSING!\n";
}

echo "Database check complete.\n";
