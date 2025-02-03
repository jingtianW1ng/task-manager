<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id");
        $stmt->execute([
            ':task_id' => $task_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        echo "Task deleted successfully";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>