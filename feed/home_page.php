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
	$getPapersQuery = "SELECT 
	posts.*, 
	users.first_name, 
	users.last_name, 
	AVG((pr.scale_id_thought_quality + pr.scale_id_connection_response +
		 pr.scale_id_idea_organization + pr.scale_id_language_accuracy +
		 pr.scale_id_references_citations) / 5) AS avg_rating
	FROM posts
	INNER JOIN users ON posts.author_id = users.user_id
	LEFT JOIN post_ratings AS pr ON posts.post_id = pr.post_id
	WHERE posts.privacy = 'public' GROUP BY posts.post_id
		ORDER BY posts.published_at DESC LIMIT 5";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="home_page.css">
	<script src="home_page.js"></script>
	<title></title>
</head>
<body>
	<?php
		echo "<script>\n";
		echo "initializeLoginStatus($loggedIn);\n";
		echo 'initializeSearchQuery(' . json_encode($getPapersQuery) . ');';
		echo "</script>\n";
	?>
	<div class="header">
	<div class="account">
			<?php
				if ($loggedIn){
					echo "<a href='../profile/account_view.php?UserId={$_SESSION['user_id']}'>";
				}else{
					echo "<a href='../login/log_in_page.html'>";
				}
					if (isset($_SESSION['image_dir'])) {
						echo "<img class='username_image' src='../{$_SESSION['image_dir']}' alt='User Image'>";
					} else {
						echo "<img class='username_image' src='../imgDirectory/default.jpg' alt='Default Image'>";
					}
				echo "</a>";
			?>
			<p class="username"><?php 
				if ($loggedIn) {
					echo htmlspecialchars($_SESSION['user_name']);
				} else {
					echo "<a href='../login/log_in_page.html'>";
					echo "Please Login first";
					echo "</a>";
				}
			 ?>
			</p>
		</div>
		<a id="stellae" href="../feed/home_page.php">Stellae</a>
		<p class="team_name">Heavenly Bodies</p>
	</div>
	<div id="feedOverall">
		<div class="hero_page" id="hero_welcome_page">
			<div class="sub_hero_page">
				<p class="hero_title">Stellae</p>
				<div class="options">
					<button onclick='autoScroll()' class="option1">Round Table</button>
					<button onclick="location.href='../postCreation/post_creation.php'" class="option1">Arcanic Prompts</button>
					<button onclick='competitionLogic()' class="option1">Enigmatic Bouts</button>
				</div>
				<img class="wizard_png" src="pictures\Your paragraph text (1) (1)-PhotoRoom.png-PhotoRoom.png">
			</div>
		</div>
		<div class="whole_user_with_main_connection">
		<div class="main_connection">
		<?php 
			if(isset($_SESSION['user_id'])){
				echo '<button onclick="redirectToPage(0, ' . $_SESSION['user_id'] . ')" class="connection_btn">Home</button>';
				echo '<button onclick="redirectToPage(1, ' . $_SESSION['user_id'] . ')" class="connection_btn connection_btn1">My Account</button>';
				echo '<button onclick="redirectToPage(2, ' . $_SESSION['user_id'] . ')" class="connection_btn connection_btn2">Add Paper</button>';
				echo '<button onclick="redirectToPage(3, ' . $_SESSION['user_id'] . ')" class="connection_btn connection_btn3">Log Out</button>';
			}else {
				echo '<button onclick="alert(\'PLEASE LOGIN\')" class="connection_btn">Home</button>';
				echo '<button onclick="alert(\'PLEASE LOGIN\')" class="connection_btn connection_btn1">My Account</button>';
				echo '<button onclick="alert(\'PLEASE LOGIN\')" class="connection_btn connection_btn2">Add Paper</button>';
				echo '<button onclick="alert(\'PLEASE LOGIN\')" class="connection_btn connection_btn3">Log Out</button>';
			}
			
		?>
		</div>
			<div id="paper_top_paper" class="paper_top_paper"> <!-- Top Papers -->
				<div class="all_filters">
					<div class="search_filter_container">
						<input type="text" id="searchBar" name="search" class="search_filter">
						<button class="search_it_btn" onclick="searchFor()">search</button>
					</div>
					<div class="search_sort_div">
						<div class="filters_check">
							<p>Search for: </p>
							<?php 
								$filters = array('Title','Author','Tags');
								foreach($filters as $filter){
									echo '<div class="checkbox_filter">';
										echo "<input type='checkbox' id='$filter' name='${filter}'/>";
										echo "<label>${filter}</label>";
									echo '</div>';
								}
							?>
						</div>
						<div class="filters_check">
							<p>Sort by: </p>
							<form id='sorting'>
							<?php 
								$filters = array('Recent','Highest Rated');
								foreach($filters as $filter){
									// echo '<div class="checkbox_filter">';
										echo "<input type='radio' id='${filter}' name='sortBy' value=${filter}/>";
										echo "<label>${filter}</label>";
									// echo '</div>';
								}
							?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>intializeCheckBox()</script>
</body>
</html>