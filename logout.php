<?php
setcookie("auth", 0, time()-1);
setcookie("admin", 0, time()-1);
header("location: ./index.php");
?>
