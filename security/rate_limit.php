<?php
/** set failed attempts */
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

/** set limit time */
if ($_SESSION['login_attempts'] > 5) {
    if (!isset($_SESSION['lockout_time'])) {
        $_SESSION['lockout_time'] = time() + 600; // 600s = 10min
    }
    if (time() < $_SESSION['lockout_time']) {
        die("Too many failed login attempts. Try again later.");
    } else {
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}
?>