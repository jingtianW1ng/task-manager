<?php
require_once 'config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<p class='success-message'>Login successful! Redirecting...</p>";
            echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);</script>";
            exit;
        } else {
            echo "<p class='error-message'>Invalid email or password.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error-message'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <h1>Login</h1>
    
    <div class="form-container">
        <h2>Sign In</h2>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
        </form>
    </div>
</body>
</html>