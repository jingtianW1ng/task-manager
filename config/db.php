<?php
$host = 'sql306.infinityfree.com';  // host name
$dbname = 'if0_38266485_task_manager';  // database name
$username = 'if0_38266485';  // database username
$password = 'cIafwyJzSmjG';  // database pwd
$charset = 'utf8mb4';

// set PDO connect
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 设定异常模式
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 设定默认取数据模式
        PDO::ATTR_EMULATE_PREPARES => false // 关闭模拟预处理，提高安全性
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>