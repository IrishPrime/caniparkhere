<?php
session_start();
unset($_SESSION["auth"]);
unset($_SESSION["admin"]);
$_SESSION = array();
session_destroy();
header("location: ./index.php");
?>
