<?php
session_start();

$host = 'localhost';
$username = 'Romand';
$password = 'Romand';
$database = 'stellae';

$conn = mysqli_connect($host, $username, $password, $database);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['UserId'])) {
        $UserId = $_POST['UserId'];

        header('Location: Profile/account_view.php?UserId=' . urlencode($UserId));
        exit();
    }
}

if (!isset($_SESSION['user_id'])){
    header("Location: logIn/log_in_page.html");
    exit();
}else{
    header("Location: feed/home_page.php");
    exit();
}
?> 