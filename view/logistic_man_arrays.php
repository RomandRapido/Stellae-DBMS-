<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		session_start();
		$host = 'localhost';
		$username = 'Romand';
		$password = 'Romand'; 
		$database = 'stellae';

		$conn = mysqli_connect($host, $username, $password, $database);
		if (!$conn){
			die ("Cannot connect to database");
		}
		if (isset($_SESSION['user_id'])) {
			$loggedIn = true;
		} else {
			$loggedIn = false; 
		}

		$postId = $_POST['postId'];
			$action = isset($_POST['action']) ? $_POST['action'] : '';
				switch ($action) {
					case 'get_comments':
						get_comments($conn, $postId);
						break;
					case 'get_word_contents':
						get_word_contents($conn, $postId);
						break;
					default:
						break;
				}
			}
		function get_word_contents($conn,$postId){
			$getPapersQuery = "SELECT posts.*, users.first_name, users.last_name, users.image_dir FROM posts INNER JOIN users ON posts.author_id = users.user_id WHERE post_id = $postId";
			$papersResult = mysqli_query($conn, $getPapersQuery);
			if (!$papersResult) { die("Query for papers failed."); }
			
			$postScheme = mysqli_fetch_assoc($papersResult);
			if ($postScheme['author_id'] != $_SESSION['user_id']){
				if ($postScheme['privacy'] == "private"){
					die ("Not authored private paper");
				}
			}
			
			$interestQuery = "SELECT interests.interest_name
							FROM post_interests
							JOIN interests ON post_interests.interest_id = interests.interest_id
							WHERE post_interests.post_id = " . $postScheme['post_id'];

			$interestResult = mysqli_query($conn, $interestQuery);

			if (!$interestResult) {
				die("Query for interests failed: " . mysqli_error($conn));
			}

			$interestsArray = array();

			while ($interestRow = mysqli_fetch_assoc($interestResult)) {
				$interestsArray[] = $interestRow['interest_name'];
			}
			$searchLikeQuery = "SELECT * FROM `post_likes`
								WHERE post_id = {$postScheme['post_id']} and user_liker = {$_SESSION['user_id']}";
							
			$searchLikeResult = mysqli_query($conn, $searchLikeQuery);

			if (!$searchLikeResult) {
				die("Query for searching like failed.");
			}

			$initial_liked = (mysqli_num_rows($searchLikeResult) > 0);
		
			$likeCountsQuery = "SELECT * FROM `post_likes`
								WHERE post_id = {$postScheme['post_id']}";
								$likeCountsResult = mysqli_query($conn, $likeCountsQuery);
			
			
			$paper = array(
				'post_id' => $postScheme['post_id'],
				'title' => $postScheme['title'],
				'author_name' => $postScheme['first_name'] . ' ' . $postScheme['last_name'],
				'published_date' => DateTime::createFromFormat('Y-m-d H:i:s', $postScheme['published_at'])->format("M d, Y D"),
				'content' => $postScheme['content'],
				'interests' => $interestsArray,
				'initial_liked' => $initial_liked,
				'likes' => mysqli_num_rows($likeCountsResult),
				'image_dir' => $postScheme['image_dir'] ?? 'imgDirectory/default.jpg',
				'author_id' => $postScheme['author_id']
			);
			echo json_encode($paper);
		}
			
		function get_comments($conn, $postId){
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
					
					$commentDetails = [
						'author' => $authorName,
						'date' => DateTime::createFromFormat('Y-m-d H:i:s', $comment['published_date'])->format("M d, Y D H:i"),
						'content' => $comment['content'],
						'author_id' => $commenterId
					];
					
					$commentsArray[] = $commentDetails;
				}
			}
			echo json_encode($commentsArray);
			}
?>