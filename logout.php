<?php
session_start();
session_destroy();
unset($_SESSION);

setcookie("auth", 0, time()-3600);
setcookie("admin", 0, time()-3600);
setcookie("id", 0, time()-3600);
unset($_COOKIE);

header("location: ./index.php");
?>
