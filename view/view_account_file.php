<?php
	session_start();
	$host = 'localhost';
	$username = 'Romand';
	$password = 'Romand'; 
	$database = 'stellae';

	$conn = mysqli_connect($host, $username, $password, $database);

	if (!isset($_GET['PostId'])) {
		// header('Location: index.php');
		// exit();
		echo "no postId";
	}

	if ($_SESSION['user_id']){
		$loggedIn = true;
	}else{
		header('Location: ../logIn/log_in_page.html');
		exit();
	}
	
	$postId = $_GET['PostId'];
	$query = "SELECT * FROM posts WHERE post_id = $postId";
	$result = mysqli_query($conn, $query);

	if ($result && $user = mysqli_fetch_assoc($result)) {
	}

	if ($loggedIn){
		$searchLikeQuery = "SELECT * FROM `post_likes`
						WHERE post_id = $postId and user_liker = {$_SESSION['user_id']}";
						
		$searchLikeResult = mysqli_query($conn, $searchLikeQuery);

		if (!$searchLikeResult) {
			die("Query for searching like failed.");
		}

		$initial_liked = (mysqli_num_rows($searchLikeResult) > 0);
	}else{
		$initial_liked = false;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="view_account_file.css">
	<title></title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- <script>postContents();</script> -->
</head>
<body>
	<div class="header">
	<div class="account">
			<?php
				echo "<a href='../profile/account_view.php?UserId={$_SESSION['user_id']}'>";
					if ($_SESSION['image_dir']) {
						echo "<img class='username_image' src='../{$_SESSION['image_dir']}' alt='User Image'>";
					} else {
						echo "<img class='username_image' src='../imgDirectory/default.jpg' alt='Default Image'>";
					}
				echo "</a>";
			?>
			<p class="username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
		</div>
		<a id="stellae" href="../feed/home_page.php">Stellae</a>
		<p class="team_name">Heavenly Bodies</p>
	</div>
	<div class="whole_user_with_main_connection">
	<div class="main_connection">
		<?php 
			echo '<button onclick="redirectToPage(0, ' . $_SESSION['user_id'] . ')" class="connection_btn">Home</button>';
			echo '<button onclick="redirectToPage(1, ' . $_SESSION['user_id'] . ')" class="connection_btn connection_btn1">My Account</button>';
			echo '<button onclick="redirectToPage(2, ' . $_SESSION['user_id'] . ')" class="connection_btn connection_btn2">Add Paper</button>';
			echo '<button onclick="redirectToPage(3, ' . $_SESSION['user_id'] . ')" class="connection_btn connection_btn3">Log Out</button>';
			?>
		</div>
		<div>
			<div id="account_profile_info">
			</div>
			<div id='all_paper_pages' class="all_paper_pages">
			</div>
			<div class="butns_n_dropdown_parent">
				<div class="butns_n_dropdown">
					<?php 
						$button_options = array('Like 👍', 'Comment 💬', 'Rate🌟');
						foreach ($button_options as $ind => $option) {
							if ($ind==1){
								echo "<button onclick='initializeCommentSection($postId)' class='comment_button action_btns'>${option}</button>";
							}else if ($ind==0){
								echo "<button id='buttonLike' onclick='toggleLike($postId)' class='comment_button action_btns'>$option</button>";
							}
							else{
								echo "<button id=$option onclick='enable_dropfeature(`rating_section`, $postId)' class='comment_button action_btns'>${option}</button>";
							}
						}
					?>
				</div>
				<div class="butns_n_dropdown" id="rating_section">
				</div>
				<div id="partial_comment_sec">
				</div>
			</div>
		</div>
	</div>
	<script src="view_account_file.js"></script>
	<?php 
		echo '<script>postContents('.$postId.')</script>';
		echo '<script>initializeLoginStatus('.$loggedIn.')</script>';
		echo '<script>initializeLikedStatus('.$initial_liked.')</script>';
	?>
</body>
</html>