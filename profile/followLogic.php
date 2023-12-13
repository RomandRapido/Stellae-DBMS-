<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'localhost';
    $username = 'Romand';
    $password = 'Romand'; 
    $database = 'stellae';

    $conn = mysqli_connect($host, $username, $password, $database);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    $following_id = $_POST['user_id'];
    echo $following_id;

    $searchFollowQuery = "SELECT * FROM `followers`
    WHERE following_id = $following_id  and follower_id = {$_SESSION['user_id']}";
    $searchFollowResult = mysqli_query($conn, $searchFollowQuery);


    if (!$searchFollowResult) { die("Query for searching follow failed."); }

    if (mysqli_num_rows($searchFollowResult) == 0) {
        //no follow
        $followQuery = "INSERT INTO followers (following_id, follower_id)
        VALUES ($following_id, {$_SESSION['user_id']});";
    } else {
        //yes like
        $followQuery = "DELETE FROM followers WHERE following_id = $following_id  and follower_id = {$_SESSION['user_id']};";
    }
    $followResult = mysqli_query($conn, $followQuery);
    
    if (!$followResult) { die("Query for follow insertion or deletion failed."); }
}

?> 