<?php
session_start();
require_once 'config/db.php';

// check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get user id
$user_id = $_SESSION['user_id'];

// get current task
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>