<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/db.php';
require_once __DIR__ . '/security/security.php';

// make sure already login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get task test
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

    <!-- css style -->
    <link rel="stylesheet" href="css/style.css">

    <script>
        // add task（AJAX）
        function addTask(event) {
            event.preventDefault();

            let title = document.getElementById("title").value;
            let dueDate = document.getElementById("due_date").value;
            let description = document.getElementById("description").value;
            let csrfToken = document.getElementById('csrf_token').value; // ✅ 读取 CSRF 令牌

            if (!title || !dueDate) {
                alert("Title and Due Date are required!");
                return;
            }

            fetch("api/add_task.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `title=${encodeURIComponent(title)}&due_date=${encodeURIComponent(dueDate)}&description=${encodeURIComponent(description)}&csrf_token=${encodeURIComponent(csrfToken)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("message").innerText = data.message;

                    // add new task to list
                    let taskList = document.getElementById("task-list");
                    let newTask = document.createElement("li");
                    newTask.id = `task-${data.task_id}`;
                    newTask.classList.add("task-item", "task-pending"); 

                    newTask.innerHTML = `
                        <div class="task-content">
                            <strong>${title}</strong> - pending (Due: ${dueDate})
                            <p class="task-desc" id="desc-${data.task_id}" style="display: none;">
                                ${description.replace(/\n/g, "<br>")}
                            </p>
                            <button class="toggle-desc-btn" onclick="toggleDescription(${data.task_id})">Show Details</button>
                        </div>
                        <div class="task-actions">
                            <select onchange="updateTaskStatus(${data.task_id}, this.value)">
                                <option value="pending" selected>Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <button class="delete-btn" onclick="deleteTask(${data.task_id})">Delete</button>
                        </div>
                    `;
                    taskList.appendChild(newTask);

                    // empty input
                    document.getElementById("title").value = "";
                    document.getElementById("due_date").value = "";
                    document.getElementById("description").value = "";
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
        // update status（AJAX）
        function updateTaskStatus(taskId, newStatus) {
            // load CSRF token
            let csrfToken = document.getElementById('csrf_token').value;

            fetch("api/update_task.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `task_id=${taskId}&new_status=${newStatus}&csrf_token=${encodeURIComponent(csrfToken)}`
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

        // delete task（AJAX）
        function deleteTask(taskId) {
            if (!confirm("Are you sure you want to delete this task?")) {
                return;
            }

            // load CSRF token
            let csrfToken = document.getElementById('csrf_token').value;

            fetch("api/delete_task.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `task_id=${taskId}&csrf_token=${encodeURIComponent(csrfToken)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById(`task-${taskId}`).remove();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }

        //toggle description
        function toggleDescription(taskId) {
            let desc = document.getElementById("desc-" + taskId);
            let btn = document.querySelector(`#task-${taskId} .toggle-desc-btn`);

            if (desc.style.display === "none") {
                desc.style.display = "block";
                btn.innerText = "Hide Details";
            } else {
                desc.style.display = "none";
                btn.innerText = "Show Details";
            }
        }
    </script>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <div id="message"></div>

    <!--  add CSRF token let JavaScript load -->
    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <!-- add task -->
    <h2>Add Task</h2>
    <form onsubmit="addTask(event);">
        <label for="title">Task Title:</label>
        <input type="text" name="title" id="title" required><br>

        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required><br>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="3" placeholder="Enter additional details..."></textarea><br>

        <button type="submit">Add Task</button>
    </form>

    <!-- task diagram -->
    <h2>Your Tasks</h2>
    <ul id="task-list">
        <?php foreach ($tasks as $task): ?>
            <li id="task-<?php echo $task['id']; ?>" class="task-item task-<?php echo $task['status']; ?>">
                <div class="task-content">
                    <div class="task-header">
                        <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                        <small>(Due: <?php echo htmlspecialchars($task['due_date']); ?>)</small>
                    </div>

                    <p class="task-desc" id="desc-<?php echo $task['id']; ?>" style="display: none;">
                        <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                    </p>
                    <button class="toggle-desc-btn" onclick="toggleDescription(<?php echo $task['id']; ?>)">Show Details</button>
                </div>

                <div class="task-actions">
                    <select onchange="updateTaskStatus(<?php echo $task['id']; ?>, this.value)">
                        <option value="pending" <?php if ($task['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="in_progress" <?php if ($task['status'] === 'in_progress') echo 'selected'; ?>>In Progress</option>
                        <option value="completed" <?php if ($task['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                    </select>

                    <button class="delete-btn" onclick="deleteTask(<?php echo $task['id']; ?>)">Delete</button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>