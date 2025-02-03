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
    <script>
        // AJAX update task status
        function updateTaskStatus(taskId, newStatus) {
            fetch("api/update_task.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `task_id=${taskId}&new_status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("message").innerText = data.message;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }

        // AJAX delete task
        function deleteTask(taskId) {
            if (!confirm("Are you sure you want to delete this task?")) {
                return;
            }

            fetch("api/delete_task.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `task_id=${taskId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById(`task-${taskId}`).remove(); // 直接从 DOM 中删除
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    </script>
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

    <div id="message"></div>

    <!-- task diagram -->
    <h2>Your Tasks</h2>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li id="task-<?php echo $task['id']; ?>">
                <strong><?php echo htmlspecialchars($task['title']); ?></strong> - 
                <?php echo htmlspecialchars($task['status']); ?>

                <!-- 任务状态更新 -->
                <select onchange="updateTaskStatus(<?php echo $task['id']; ?>, this.value)">
                    <option value="pending" <?php if ($task['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="in_progress" <?php if ($task['status'] === 'in_progress') echo 'selected'; ?>>In Progress</option>
                    <option value="completed" <?php if ($task['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                </select>

                <!-- 删除任务 -->
                <button onclick="deleteTask(<?php echo $task['id']; ?>)">Delete</button>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>