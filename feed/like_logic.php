<?php
session_start();
header('Content-Type: application/json');

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
    echo $_POST['post_id'];
    $post_id = $_POST['post_id'];
    $searchLikeQuery = "SELECT * FROM `post_likes`
    WHERE post_id = $post_id  and user_liker = {$_SESSION['user_id']}";
    $searchLikeResult = mysqli_query($conn, $searchLikeQuery);


    if (!$searchLikeResult) { die("Query for searching like failed."); }

    if (mysqli_num_rows($searchLikeResult) == 0) {
        //no like
        $likeQuery = "INSERT INTO post_likes (post_id, user_liker)
        VALUES ($post_id, {$_SESSION['user_id']});";
    } else {
        //yes like
        $likeQuery = "DELETE FROM post_likes WHERE post_id = $post_id  and user_liker = {$_SESSION['user_id']};";
    }
    $likeResult = mysqli_query($conn, $likeQuery);
    
    if (!$likeResult) { die("Query for like insertion or deletion failed."); }
}

?> 