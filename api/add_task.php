<?php
require_once '../config/db.php';
require_once __DIR__ . '/../security/security.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['due_date'])) {
    // check CSRF toekn
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(["status" => "error", "message" => "CSRF token validation failed"]));
    }

    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    if (empty($title) || empty($due_date)) {
        echo json_encode(["status" => "error", "message" => "Title and Due Date are required."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, due_date, description, status) VALUES (:user_id, :title, :due_date, :description, 'pending')");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title' => $title,
            ':due_date' => $due_date,
            ':description' => $description
        ]);

        $task_id = $pdo->lastInsertId();
        echo json_encode(["status" => "success", "message" => "Task added successfully!", "task_id" => $task_id]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error adding task."]);
    }
}
?>