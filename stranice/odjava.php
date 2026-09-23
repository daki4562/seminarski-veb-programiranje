<?php
session_start();

// Brišu se svi podaci iz sesije
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Sesija se uništava
session_destroy();

header('Location: prijava.php');
exit;
