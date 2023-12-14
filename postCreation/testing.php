<?php
    session_start();
    header('Content-Type: application/json');
    $host = 'localhost';
    $userName = 'Romand';
    $password = 'Romand';
    $dbName = 'stellae';

    $conn = mysqli_connect($host, $userName, $password, $dbName);
    date_default_timezone_set('Asia/Manila');

    if (!$conn) {
        $error = ['error' => 'Connection failed: ' . mysqli_connect_error()];
        echo json_encode($error);
        exit;
    }

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $privacy = mysqli_real_escape_string($conn, $_POST['buttonClicked']);
    $preview = mysqli_real_escape_string($conn, $_POST['preview']);

    $published_at = date('Y-m-d H:i:s');
    if (isset($_POST['postId'])){
        $doQuery =  "UPDATE posts 
            SET title = '$title',
                updated_at = '$published_at',
                content = '$content',
                privacy = '$privacy',
                preview = '$preview'
            WHERE post_id = {$_POST['postId']}";
    
        $deletePostInterestQuery = "DELETE FROM post_interests 
            WHERE post_id = {$_POST['postId']}";
    
        $deletePostInterestResult = mysqli_query($conn, $deletePostInterestQuery);
        if (!$deletePostInterestResult) {
            die("Query for deleting interests failed: " . mysqli_error($conn));
        }

    }else{
        $doQuery = "INSERT INTO posts (author_id, title, updated_at, published_at, content, privacy, preview) 
                        VALUES ({$_SESSION["user_id"]}, '$title', NULL, '$published_at', '$content', '$privacy', '$preview')";
    }
    $result = mysqli_query($conn, $doQuery);

    if ($result) {
        if (isset($_POST['postId'])){
            $post_id = $_POST['postId'];
        }else{
            $post_id = mysqli_insert_id($conn);
        }

        $interestsString = $_POST['interests'];
        if(!(empty(trim($interestsString)))){
            $interestsArray = explode(",", $interestsString);
            
            foreach ($interestsArray as $interest) {
                $interest = ucwords(trim($interest));
                if ((empty($interest))){
                    continue;
                }
                $checkIfExistsQuery = "SELECT interest_id
                                    FROM interests
                                    WHERE LOWER(interest_name) = LOWER('" . mysqli_real_escape_string($conn, $interest) . "')";
                $resultInterest = mysqli_query($conn, $checkIfExistsQuery);
                
                if ($resultInterest) {
                    if (mysqli_num_rows($resultInterest) > 0) {
                        $interestFetched = mysqli_fetch_assoc($resultInterest);
                        $interest_id = $interestFetched['interest_id'];
                    } else {
                        $insertInterestQuery = "INSERT INTO interests (interest_name)
                                                VALUES ('" . mysqli_real_escape_string($conn, $interest) . "')";
                        $resultInsertInterest = mysqli_query($conn, $insertInterestQuery);
                        $interest_id = mysqli_insert_id($conn);
                    }

                    $insertPostInterestQuery = "INSERT INTO post_interests (post_id, interest_id)
                                                VALUES ($post_id, $interest_id)";
                    $resultInsertPostInterest = mysqli_query($conn, $insertPostInterestQuery);
                    
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            }
        }

        $response = ['message' => 'Data inserted successfully', 'post_id' => $post_id];
        echo json_encode($response);
    } else {
        $error = ['error' => 'Error inserting post: ' . mysqli_error($conn)];
        echo json_encode($error);
    }
    
    mysqli_close($conn);
?>