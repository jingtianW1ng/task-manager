<?php
require_once '../config/db.php';
session_start();

// make sure user already login
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['due_date'])) {
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];

    if (empty($title) || empty($due_date)) {
        die("Title and Due Date are required.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, due_date) VALUES (:user_id, :title, :due_date)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title' => $title,
            ':due_date' => $due_date
        ]);
        echo "Task added successfully";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>