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
        $post_id = $_POST['postId'];

        mysqli_begin_transaction($conn);

        try {
            $tablesToDeleteFrom = ['post_comments', 'post_interests', 'post_likes', 'post_ratings'];
            foreach ($tablesToDeleteFrom as $table) {
                $deleteQuery = "DELETE FROM $table WHERE post_id = $post_id";
                mysqli_query($conn, $deleteQuery);
            }

            $deletePostQuery = "DELETE FROM posts WHERE post_id = $post_id";
            mysqli_query($conn, $deletePostQuery);

            mysqli_commit($conn);

            echo json_encode(['success' => 'Post and related entries deleted successfully']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete post and related entries']);
        }

        mysqli_close($conn);
    }
?>
