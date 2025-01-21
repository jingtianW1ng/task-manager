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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

    <!-- add task disgram -->
    <h2>Add Task</h2>
    <form method="POST" action="">
        <label for="title">Task Title:</label>
        <input type="text" name="title" id="title" required><br>
        <label for="description">Description:</label>
        <textarea name="description" id="description"></textarea><br>
        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required><br>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <!-- show task diagram -->
    <h2>Your Tasks</h2>
    <?php if (!empty($tasks)): ?>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li>
                    <strong><?php echo htmlspecialchars($task['title']); ?></strong> - 
                    <?php echo htmlspecialchars($task['status']); ?> 
                    (Due: <?php echo htmlspecialchars($task['due_date']); ?>)
                    <a href="?delete_task=<?php echo $task['id']; ?>">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tasks found. Add your first task!</p>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>