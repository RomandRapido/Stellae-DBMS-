<?php 
    session_start();
    $host = 'localhost';
    $username = 'Romand';
    $password = 'Romand'; 
    $database = 'stellae';
    $conn = mysqli_connect($host, $username, $password, $database);
    if (isset($_SESSION['user_id'])) {
		$loggedIn = true;
	} else {
		$loggedIn = false; 
	}
    $getPapersQuery = $_GET['custom_query']. ' OFFSET '. $_GET['offset'] ;
    $papersResult = mysqli_query($conn, $getPapersQuery);
    
    if (!$papersResult) { die("Query for papers failed."); }
    if (mysqli_num_rows($papersResult) === 0) { die();}
    $papersArray = array();
    
    for ($i = 0; $i < 5 && $row = mysqli_fetch_assoc($papersResult); $i++) {
    $interestQuery = "SELECT interests.interest_name
                    FROM post_interests
                    JOIN interests ON post_interests.interest_id = interests.interest_id
                    WHERE post_interests.post_id = " . $row['post_id'];

    $interestResult = mysqli_query($conn, $interestQuery);
    
    if (!$interestResult) {
        die("Query for interests failed: " . mysqli_error($conn));
    }

    $interestsArray = array();

    while ($interestRow = mysqli_fetch_assoc($interestResult)) {
        $interestsArray[] = $interestRow['interest_name'];
    }
    if ($loggedIn){
        $searchLikeQuery = "SELECT * FROM `post_likes`
        WHERE post_id = {$row['post_id']} and user_liker = {$_SESSION['user_id']}";
        $searchLikeResult = mysqli_query($conn, $searchLikeQuery);

        if (!$searchLikeResult) {
        die("Query for searching like failed.");
        }

        $initial_liked = (mysqli_num_rows($searchLikeResult) > 0);
    }else{
        $initial_liked = false;
    }
    

    $likeCountsQuery = "SELECT * FROM `post_likes`
                        WHERE post_id = {$row['post_id']}";
                        $likeCountsResult = mysqli_query($conn, $likeCountsQuery);

    $paper = array(
        'post_id' => $row['post_id'],
        'title' => $row['title'],
        'author_name' => $row['first_name'] . ' ' . $row['last_name'],
        'published_date' => DateTime::createFromFormat('Y-m-d H:i:s', $row['published_at'])->format("M, d, Y D"),
        'preview' => $row['preview']. "...",
        'interests' => $interestsArray,
        'initial_liked' => $initial_liked,
        'likes' => mysqli_num_rows($likeCountsResult),
        'avg_rating' => number_format($row['avg_rating'], 2),
        'author_id' => $row['author_id']
    );
    
    $papersArray[] = $paper;
    // print_r($papersArray);
    }
	foreach($papersArray as $index => $paper){
        $offsetIndex = $_GET['offset'] + $index;
		$pointer = "rate_n_comment_section${offsetIndex}";
		echo '<div class="paper_for_each">';
			echo '<div>';
				echo '<div class="top_most_part_feed">';
					echo "<p class='paper_title'>${paper['title']}</p>";
					echo '<div class="btns_for_action">';
						echo "<button id='likeButton${offsetIndex}' class='up_button action_btns' onclick='toggleLike(${offsetIndex}, ${paper['post_id']})'>👍</button>";
						echo "<p id='likesCount${offsetIndex}'>${paper['likes']}</p>";
						//echo "<button onclick='enable_dropfeature(0,`${pointer}`)' class='comment_button action_btns'>Comment 💬</button>";
						//echo "<button onclick='enable_dropfeature(1,`${pointer}`)' class='rate_button action_btns'>Rate🌟</button>";
					echo '</div>';
				echo '</div>';
				echo "<p class='paper_author'>${paper['author_name']}</p>";
				echo "<p class='paper_date'>${paper['published_date']}</p>";
                echo "<p class='paper_rating'>Avg Rating: ${paper['avg_rating']}</p><br>";
			echo '</div>';
			echo '<div>';
				echo "<p class='paper_abstract'>${paper['preview']}</p>";
			echo '</div>';
			echo '<div>';
				foreach($paper['interests'] as $topic_related){
					echo "<button class='topic_related_btn'>${topic_related}</button>";
				}
				echo '</div>';
			echo '<div class="user_n_vewer_options">';
				echo '<button onclick="goToViewPhp('.$paper['post_id'].')" class="ViewMore">View More</button>';
                if ($loggedIn){
                    if ($paper['author_id'] == $_SESSION['user_id']){
                        echo '<button onclick="goToEditPhp('.$paper['post_id'].')" class="ViewMore">Edit</button>';
                        echo '<button onclick="deleteThisPaper('.$paper['post_id'].')" class="ViewMore">Delete</button>';
                    }
                }
			echo '</div>';
				echo "<div class='rating_section' id = ${pointer}>";
			echo '</div>';
            echo '<script>';
			echo "initializeLikedStatus(${offsetIndex}, " . ($paper['initial_liked'] ? 'true' : 'false') . ");";
			echo '</script>';
		echo '</div>';
	}
?>