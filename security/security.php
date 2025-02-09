<?php
session_start();

/** force https avoid MITM */
//if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//    exit();
//}

/** close error report avoid information leak */
ini_set('display_errors', 0);
error_reporting(E_ALL);

/** set safe https header（avoid XSS, Clickjacking） */
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

/** avoid XSS */
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/** avoid sql injection */
function safe_query($pdo, $query, $params = []) {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

/** load csrf*/
require_once __DIR__ . '/csrf.php';

/** load rate limit */
require_once __DIR__ . '/rate_limit.php';

?>