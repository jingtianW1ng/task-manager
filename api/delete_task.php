<?php
require_once '../config/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id");
        $stmt->execute([
            ':task_id' => $task_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        echo json_encode(["status" => "success", "message" => "Task deleted successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error deleting task"]);
    }
}
?>