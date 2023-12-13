<?php 
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        $postId = intval($_POST['postId']);
        $raterId = $_SESSION['user_id'];

        $postId = intval($_POST['postId']);
        $raterId = $_SESSION['user_id'];
        $findIfRatingExistsQuery = "SELECT * FROM `post_ratings` WHERE user_id = $raterId AND post_id = $postId";
        $findIfRatingExistsResult = mysqli_query($conn, $findIfRatingExistsQuery);
        if (!$findIfRatingExistsResult) { die("Query for finding if rating exists failed.");}
        if (mysqli_num_rows($findIfRatingExistsResult)>0){
            $insertingOrUpdatingQuery = "UPDATE post_ratings SET
            scale_id_thought_quality = {$_POST['points0']},
            scale_id_connection_response = {$_POST['points1']},
            scale_id_idea_organization = {$_POST['points2']},
            scale_id_language_accuracy = {$_POST['points3']},
            scale_id_references_citations = {$_POST['points4']}
            WHERE user_id = $raterId AND post_id = $postId";
        }else{
            $insertingOrUpdatingQuery = "INSERT INTO post_ratings (user_id, post_id, scale_id_thought_quality, scale_id_connection_response, scale_id_idea_organization, scale_id_language_accuracy, scale_id_references_citations)
            VALUES ($raterId, $postId, {$_POST['points0']}, {$_POST['points1']}, {$_POST['points2']}, {$_POST['points3']}, {$_POST['points4']})";
        }
        $insertingOrUpdatingResult = mysqli_query($conn, $insertingOrUpdatingQuery);
        if ($insertingOrUpdatingResult){
            echo "rating success!";
        } else{
            die("Query for inserting or updating failed.");
        }
        
        if (!(empty(trim($_POST['comment'])))){
            $comment = $_POST['comment'];
            header("Location: addComment.php?content=" . urlencode($comment) . "&postId=" . urlencode($postId));
        }else{
            header("Location: view_account_file.php?PostId=$postId");
        }
    }
?>