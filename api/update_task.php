<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['new_status'])) {
    $task_id = intval($_POST['task_id']);
    $new_status = $_POST['new_status'];

    $valid_statuses = ['pending', 'in_progress', 'completed'];
    if (!in_array($new_status, $valid_statuses)) {
        die("Invalid status.");
    }

    try {
        $stmt = $pdo->prepare("UPDATE tasks SET status = :new_status WHERE id = :task_id AND user_id = :user_id");
        $stmt->execute([
            ':new_status' => $new_status,
            ':task_id' => $task_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        echo "Task updated successfully";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>