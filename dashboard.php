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

// add task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    if (!empty($title) && !empty($due_date)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':title' => $title,
                ':description' => $description,
                ':due_date' => $due_date
            ]);
            header("Location: dashboard.php");
            exit;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        echo "Title and due date are required.";
    }
}

// delete task
if (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id");
        $stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>