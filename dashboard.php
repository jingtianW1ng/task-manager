<?php
session_start();
require_once 'config/db.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 获取当前用户 ID
$user_id = $_SESSION['user_id'];

?>