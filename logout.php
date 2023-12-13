<?php 
session_start();
session_destroy();
header('Location: logIn/log_in_page.html');
exit();
?>