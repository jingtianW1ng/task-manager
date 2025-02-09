<?php
require_once __DIR__ . '/security/security.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Task Manager</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-content">
            <h1>Welcome to Task Manager</h1>
            <p>Effortlessly manage your tasks and stay organized.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn btn-secondary">Sign Up</a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features">
        <h2>Why Choose Task Manager?</h2>
        <div class="feature-container">
            <div class="feature">
                <img src="assets/images/task.png" alt="Task Management">
                <h3>Easy Task Management</h3>
                <p>Organize your tasks efficiently with our user-friendly dashboard.</p>
            </div>
            <div class="feature">
                <img src="assets/images/progress.png" alt="Track Progress">
                <h3>Track Your Progress</h3>
                <p>Monitor task completion status in real-time.</p>
            </div>
            <div class="feature">
                <img src="assets/images/security.png" alt="Secure & Private">
                <h3>Secure & Private</h3>
                <p>Your tasks are stored securely with encryption.</p>
            </div>
        </div>
    </section>

</body>
</html>