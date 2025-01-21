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

?>