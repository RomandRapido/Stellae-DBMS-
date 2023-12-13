<?php
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        session_start();
        $host = 'localhost';
        $username = 'Romand';
        $password = 'Romand'; 
        $database = 'stellae';

        $conn = mysqli_connect($host, $username, $password, $database);
        date_default_timezone_set('Asia/Manila');

        if (!($_SESSION['user_id'])){
            header('Location: ../logIn/log_in_page.html');
            exit();
        }
        $postId = $_GET['postId'];
        $commenterId = $_SESSION['user_id'];
        $content = $_GET['content'];
        $currentDate = date("Y-m-d H:i:s");

        $commentInsertQuery = "INSERT INTO post_comments (post_id, published_date, content, commenter_id)
                            VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($commentInsertQuery);

        if ($stmt) {
            $stmt->bind_param("issi", $postId, $currentDate, $content, $commenterId);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
        header("Location: view_account_file.php?PostId=$postId");
    }
    
?>