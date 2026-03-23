<?php
session_start();

$_SESSION = []; 

if (isset($_COOKIE[session_name()])) { 
    setcookie(session_name(), '', time() - 3600, '/');
}

if (isset($_COOKIE['recordar_token'])) {
    setcookie('recordar_token', '', time() - 3600, '/');
}

session_destroy();

header('Location: login.php');
exit;
?>
