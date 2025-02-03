<?php
session_start();
require_once 'config/db.php';

// make sure already login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get task
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
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
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <!-- add task -->
    <h2>Add Task</h2>
    <form method="POST" action="api/add_task.php">
        <label for="title">Task Title:</label>
        <input type="text" name="title" id="title" required><br>

        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required><br>

        <button type="submit">Add Task</button>
    </form>

    <!-- task diagram -->
    <h2>Your Tasks</h2>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <strong><?php echo htmlspecialchars($task['title']); ?></strong> - 
                <?php echo htmlspecialchars($task['status']); ?> 
                (Due: <?php echo htmlspecialchars($task['due_date']); ?>)

                <!-- updat task -->
                <form method="POST" action="api/update_task.php" style="display: inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">

                    <!-- task status -->
                    <select name="new_status" onchange="this.form.submit()">
                        <option value="pending" <?php if ($task['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="in_progress" <?php if ($task['status'] === 'in_progress') echo 'selected'; ?>>In Progress</option>
                        <option value="completed" <?php if ($task['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                    </select>
                </form>

                <!-- delete task -->
                <form method="POST" action="api/delete_task.php" style="display: inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>