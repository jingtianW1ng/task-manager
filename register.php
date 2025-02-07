<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // verify
    if (empty($username) || empty($email) || empty($password)) {
        echo "<p class='error-message'>All fields are required.</p>";
        exit;
    }

    // password hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // insert to database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
        echo "<p class='success-message'>Registration successful!</p>";
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
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css"> <!-- 引入 dashboard 样式 -->
</head>
<body>
    <h1>Register</h1>

    <div class="form-container">
        <h2>Sign Up</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>