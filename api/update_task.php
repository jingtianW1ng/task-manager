<?php
require_once '../config/db.php';
require_once __DIR__ . '/security/security.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['new_status'])) {
    // check CSRF token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(["status" => "error", "message" => "CSRF token validation failed"]));
    }
    $task_id = intval($_POST['task_id']);
    $new_status = $_POST['new_status'];

    $valid_statuses = ['pending', 'in_progress', 'completed'];
    if (!in_array($new_status, $valid_statuses)) {
        echo json_encode(["status" => "error", "message" => "Invalid status"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE tasks SET status = :new_status WHERE id = :task_id AND user_id = :user_id");
        $stmt->execute([
            ':new_status' => $new_status,
            ':task_id' => $task_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        echo json_encode(["status" => "success", "message" => "Task updated successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error updating task"]);
    }
}
?>