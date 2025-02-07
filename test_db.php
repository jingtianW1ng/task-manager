<?php
require_once 'config/db.php';

try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection is successful!";
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>