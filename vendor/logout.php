<?php

session_start();

$_SESSION['auth'] = false;
unset($_SESSION['user']);
setcookie('user', '', time(), '/' );
unset($_COOKIE['user']);

header('Location: /');

?>