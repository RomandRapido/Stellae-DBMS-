<?php
session_start();
$searchKey = "%" . strtolower(str_replace(" ", "%", $_POST['searchKey'])) . "%";
$title = isset($_POST['Title']);
$author = isset($_POST['Author']);
$tag = isset($_POST['Tags']);


$getPapersQuery = "SELECT posts.*,
    users.first_name,
    users.last_name,
    users.user_name,
    GROUP_CONCAT(interests.interest_name) AS interest_names,
    AVG((post_ratings.scale_id_thought_quality + post_ratings.scale_id_connection_response + post_ratings.scale_id_idea_organization + post_ratings.scale_id_language_accuracy + post_ratings.scale_id_references_citations)/5) AS avg_rating
    FROM posts
    INNER JOIN users ON posts.author_id = users.user_id
    LEFT JOIN post_interests ON posts.post_id = post_interests.post_id
    LEFT JOIN interests ON post_interests.interest_id = interests.interest_id
    LEFT JOIN post_ratings ON posts.post_id = post_ratings.post_id
    WHERE posts.privacy = 'public' ";

if ($title && !$author && !$tag){
    $getPapersQuery .= "AND (
        (title LIKE '$searchKey')
    )";
}else if ($author && !$title && !$tag){
    $getPapersQuery .= "AND (
        (users.user_name LIKE '$searchKey'
            OR CONCAT(users.first_name, ' ', users.last_name) LIKE '$searchKey'
            OR users.first_name LIKE '$searchKey'
            OR users.last_name LIKE '$searchKey')
    )";
}else if ($tag && !$title && !$author){
    $getPapersQuery .= "AND (
        interests.interest_name LIKE '$searchKey'
    )";
}else if ($tag && $title && !$author){
    $getPapersQuery .= "AND (
        (title LIKE '$searchKey')
        OR interests.interest_name LIKE '$searchKey'
    )";
}else if (!$tag && $title && $author){
    $getPapersQuery .= "AND (
        (title LIKE '$searchKey')
        OR (users.user_name LIKE '$searchKey'
            OR CONCAT(users.first_name, ' ', users.last_name) LIKE '$searchKey'
            OR users.first_name LIKE '$searchKey'
            OR users.last_name LIKE '$searchKey')
        OR interests.interest_name LIKE '$searchKey'
    )";
}else if ($tag && !$title && $author){
    $getPapersQuery .= "AND (
        (users.user_name LIKE '$searchKey'
            OR CONCAT(users.first_name, ' ', users.last_name) LIKE '$searchKey'
            OR users.first_name LIKE '$searchKey'
            OR users.last_name LIKE '$searchKey')
        OR interests.interest_name LIKE '$searchKey'
    )";
}else if ($tag && $title && $author){
    $getPapersQuery .= "AND (
        (title LIKE '$searchKey')
        OR (users.user_name LIKE '$searchKey'
            OR CONCAT(users.first_name, ' ', users.last_name) LIKE '$searchKey'
            OR users.first_name LIKE '$searchKey'
            OR users.last_name LIKE '$searchKey')
        OR interests.interest_name LIKE '$searchKey'
    )";
}

if ($_POST['sort'] == 'Recent'){
    $getPapersQuery .= "GROUP BY posts.post_id
    ORDER BY posts.published_at DESC LIMIT 5 ";
} else if ($_POST['sort'] == 'Highest Rated'){
    $getPapersQuery .= "GROUP BY posts.post_id
    ORDER BY avg_rating DESC LIMIT 5 ";
} else {
    $getPapersQuery .= "GROUP BY posts.post_id
    ORDER BY posts.published_at DESC LIMIT 5 ";
    //need to be changed
}


echo $getPapersQuery;
?>
