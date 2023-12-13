<?php 
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        session_start();
        $host = 'localhost';
        $username = 'Romand';
        $password = 'Romand'; 
        $database = 'stellae';

        $conn = mysqli_connect($host, $username, $password, $database);

        if (!($_SESSION['user_id'])){
            header('Location: ../logIn/log_in_page.html');
            exit();
        }

        $postId = $_GET['postId'];

        $getCommentsQuery = "SELECT * FROM `post_comments` WHERE post_id = $postId ORDER BY published_date DESC";
        $getCommentsResult = mysqli_query($conn, $getCommentsQuery);

        if (!$getCommentsResult) {
            die("Query for getting comments failed.");
        }

        $commentsArray = [];

        while ($comment = mysqli_fetch_assoc($getCommentsResult)) {
            $commenterId = $comment['commenter_id'];
            
            $commenterNameQuery = "SELECT first_name, last_name FROM users WHERE user_id = $commenterId";
            $commenterNameResult = mysqli_query($conn, $commenterNameQuery);
            
            if ($commenterNameResult && mysqli_num_rows($commenterNameResult) > 0) {
                $commenterName = mysqli_fetch_assoc($commenterNameResult);
                $authorName = $commenterName['first_name'] . ' ' . $commenterName['last_name'];
                
                // Build the array with author, date, and content
                $commentDetails = [
                    'author' => $authorName,
                    'date' => $comment['published_date'],
                    'content' => $comment['content']
                ];
                
                $commentsArray[] = $commentDetails;
            }
        }

        
    }
?>