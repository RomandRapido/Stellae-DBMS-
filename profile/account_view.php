<?php
	session_start();
	$host = 'localhost';
	$username = 'Romand';
	$password = 'Romand'; 
	$database = 'stellae';

	$conn = mysqli_connect($host, $username, $password, $database);

	if (!isset($_GET['UserId'])) {
		header('Location: index.php');
		exit();
	}

	if ($_SESSION['user_id']){
		$loggedIn = true;
	}else{
		header('Location: ../logIn/log_in_page.html');
		exit();
	}

	$userId = $_GET['UserId'];

	$query = "SELECT * FROM users WHERE user_id = $userId";
	$result = mysqli_query($conn, $query);

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
	WHERE posts.privacy = 'public' AND posts.author_id = $userId
	GROUP BY posts.post_id
	ORDER BY posts.published_at DESC LIMIT 5";

	if ($result && $user = mysqli_fetch_assoc($result)) {

	}
	$following_id = $_GET['UserId'];
	$searchFollowQuery = "SELECT * FROM `followers`
    WHERE following_id = $following_id  and follower_id = {$_SESSION['user_id']}";
    $searchFollowResult = mysqli_query($conn, $searchFollowQuery);


    if (!$searchFollowResult) { die("Query for searching like failed."); }

    if (mysqli_num_rows($searchFollowResult) == 0) {
        $initialFollow = false;
    } else {
        $initialFollow = true;
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" type="text/css" href="account_page.css">
	<link rel="stylesheet" type="text/css" href="modal_update.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="account_page.js"></script>
	<script src="modal_update.js"></script>
	<?php
echo "<script>\n";
echo "initializeSearchQuery(" . json_encode($getPapersQuery) . ");";
echo "initializeUserId(" . json_decode($userId) .");";
echo "</script>\n";
?>
</head>
<body>
	<form onsubmit='return validate_inputs(["f_name_name","l_name_name","user_name_name"],["email_input","pass_input"])' action="php_modal_connection/update_data.php" method="POST" id="update_del_form" class="form_update_delete_user open_close" enctype="multipart/form-data">
		<fieldset class="update_profile">
			<fieldset id="contain2" class="cointain">
				<img class="profile_image" id='preview' alt='preview'>
				<fieldset class="inputs">
					<input onchange="get_event(event)" type="file" name="filename" id="image">
				</fieldset>
			</fieldset>
			<fieldset class="general_info">
				<fieldset class="cointain">
					<input id='user_name_name' class="inputing" type="text" name="username">
					<label class="placeholder">UserName</label>
				</fieldset>
				<fieldset class="cointain">
					<input id='f_name_name' class="inputing" type="text" name="f_name">
					<label class="placeholder">First Name</label>
				</fieldset>
				<fieldset class="cointain">
					<input id='l_name_name' class="inputing" type="text" name="l_name">
					<label class="placeholder">Last Name</label>
				</fieldset>
				<fieldset class="cointain">
					<input id='pass_input' class="inputing" type="password" name="password_mo">
					<label class="placeholder">Password</label>
				</fieldset>
				<fieldset class="cointain">
					<input id='email_input' class="inputing" type="text" name="email">
					<label class="placeholder">Email</label>
				</fieldset>
				<fieldset class="cointain">
					<input class="inputing" type="text" name="program">
					<label class="placeholder">Program</label>
					<select name="education" class="select">
						<option value=1>High School</option>
						<option value=2>Undergraduate</option>
						<option value=3>Graduate</option> 
						<option value=4>Masteral</option> 
						<option value=5>PHD</option> 
					</select>
				</fieldset>
			</fieldset>
			<fieldset id="school_input">
				<fieldset class="input_school cointain">
					<input id="input_school_type" oninput="parse_education('container_schools')" class="inputing" type="text" name="school">
					<label id="placeholder" class="placeholder">School</label>
				</fieldset>
				<fieldset id="container_schools"></fieldset>
			</fieldset>
			<fieldset id="school_input2">
				<fieldset class="input_school cointain">
					<input oninput="parse_interest('interest_here')" id="inputing" class="inputing" type="text" name="interests">
					<label class="placeholder to_interest">Interests</label>
				</fieldset>
				<fieldset id="chosen_interest"></fieldset>
				<fieldset id="interest_here"></fieldset>
			</fieldset>
			<fieldset class="inputs">
				<input class="btn" type="submit" name="delete_submit" value="Delete Account">
				<input class="btn" type="submit" name="update_submit" value="Update Account">
				<p onclick="toggle_scale('update_del_form')" id="btn_p">Cancel</p>
			</fieldset>
			<fieldset>
				<textarea name="description" class="text_area" placeholder="Description Section" maxlength="200"></textarea>
			</fieldset>
		</fieldset>
	</form>
	<div class="header">
		<div class="account">
			<?php
				
					if ($_SESSION['image_dir']) {
						echo "<img class='username_image' src='../{$_SESSION['image_dir']}' alt='User Image'>";
					} else {
						echo "<img class='username_image' src='../imgDirectory/default.jpg' alt='Default Image'>";
					}
				
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
		<div class="account_details">
			<div class="our_user">
				<?php
				$imageQuery = "SELECT image_dir FROM users
				WHERE users.user_id = ?";

			$stmt = mysqli_prepare($conn, $imageQuery);

			if ($stmt) {
				mysqli_stmt_bind_param($stmt, "i", $userId);
				mysqli_stmt_execute($stmt);

				mysqli_stmt_bind_result($stmt, $imageDir);

				mysqli_stmt_fetch($stmt);

				mysqli_stmt_close($stmt);
				if ($imageDir) {
					echo "<img class='profile_image' src='../$imageDir' alt='User Image'>";
				} else {
					echo "<img class='profile_image' src='../imgDirectory/default.jpg' alt='Default Image'>";
				}
			} else {
				echo "Error preparing SQL statement.";
			}
				?>
				<div class="user_details">
					<div class="username_with_edit_profile">
						<p class="username_hero"><?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']) ?></p>
						<div>
							<?php
								$view_profileID = $_GET['UserId'];
								if ($view_profileID == $_SESSION['user_id']){
									echo "<button onclick='toggle_scale(`update_del_form`)' class='edit_profile'>Edit Profile</button>";
								}
							?>
							<button onclick="autoScroll()" class="view_papers_profile">View Papers</button>
						</div>
					</div>
					<div class="div_for_user_details">
						<p>
							<?php
								$schoolQuery = "SELECT school_options.school_name
								FROM users
								JOIN school_options ON users.school_id = school_options.school_id
								WHERE users.user_id = $userId;";

								$school = mysqli_query($conn, $schoolQuery);
								
								if ($school) {
									while ($row = mysqli_fetch_assoc($school)) {
										$schoolName = $row['school_name'];						
										echo '<p class="username">' . htmlspecialchars($schoolName) . '</p>';
									}
								} else {
									echo 'Query failed: ' . mysqli_error($conn);
								}
							?>
						</p>
						<p>
							<?php
								echo htmlspecialchars($user['email'])
							?>
						</p>
						<p>
							<?php
								$educationLevelQuery = "SELECT education_level_options.education_level_name
								FROM users
								JOIN education_level_options ON users.education_level_id = education_level_options.education_level_id
								WHERE users.user_id = $userId;";
								$educationLevelResult = mysqli_query($conn, $educationLevelQuery);

								if ($educationLevelResult) {
									$row = mysqli_fetch_assoc($educationLevelResult);
							
									if ($row) {
										$educationLevel = $row['education_level_name'];
									} else {
										$educationLevel = "N/A";
									}
									mysqli_free_result($educationLevelResult);
								} else {
									
								}
								echo "$educationLevel. ";
								if ($educationLevel != 'High School'){
									echo $user['program'];
								}
							?>
						</p>
						<div class="top_bottom_divs_user_related">
							<marquee class='marquee_tag_interest'>
								<?php
									$interestQuery = "SELECT
									interests.interest_name
								FROM
									user_interests
								JOIN interests ON user_interests.interest_id = interests.interest_id
								JOIN users ON user_interests.user_id = users.user_id
								WHERE
									users.user_id = $userId;";
									$interestsResult = mysqli_query($conn, $interestQuery);

									if ($interestsResult) {
										$interestsArray = array();
									
										while ($row = mysqli_fetch_assoc($interestsResult)) {
											$interestsArray[] = $row['interest_name'];
										}
									
										mysqli_free_result($interestsResult);
										
									} else {
										echo 'Query failed: ' . mysqli_error($conn);
									}

									foreach($interestsArray as $interest){
										echo "<a href=''>&#8226; ${interest}</a>";
									}
								?>
							</marquee>
							<div class="follower_following">
								<p>&#8226; 
									<?php 
										$followingQuery = "SELECT COUNT(*) AS numFollowing
										FROM followers
										WHERE follower_id = $userId;";

										$followingResult = mysqli_query($conn, $followingQuery);

										if ($followingResult) {
											$row = mysqli_fetch_assoc($followingResult);
											$followingNumbers = $row['numFollowing'];
											mysqli_free_result($followingResult);
										} else {

										}
										echo $followingNumbers;
									?> 
									Following</p>
								<p>&#8226; 
									<?php 
										$followerQuery = "SELECT COUNT(*) AS numFollowers
										FROM followers
										WHERE following_id = $userId;";

										$followerResult = mysqli_query($conn, $followerQuery);

										if ($followerResult) {
											$row = mysqli_fetch_assoc($followerResult);
											$followerNumbers = $row['numFollowers'];
											mysqli_free_result($followerResult);
										} else {

										}
										echo $followerNumbers;
									?>
								 Followers</p>
							</div>
						</div>
					</div>
					<div>
						<?php
							if ($following_id != $_SESSION['user_id']){
								echo "<button onclick='toggleFollow($following_id)' class='follow_btn'>Follow</button>";
							}
						?>
					</div>
				</div>
			</div>
			<div class="account_details_stats">
				<div class="stat_summary">
					<div class="top_stat_summary">
						<div class="numbers_stat_summary">
							<?php
								$sql_query = "SELECT COUNT(post_id) as postNum 
								FROM posts 
								WHERE author_id = {$_GET['UserId']} and privacy = 'public';;";
								$result = mysqli_query($conn,$sql_query);
								$row = mysqli_fetch_assoc($result)['postNum'];
								echo "<p class='stats'>${row}</p>"
							?>
							<p>Published Papers</p>
						</div>
						<div class="numbers_stat_summary">
							<?php
								$sql_query = "SELECT COUNT(*) as gradeNum FROM post_ratings WHERE user_id = {$_GET['UserId']};";
								$result = mysqli_query($conn,$sql_query);
								$row = mysqli_fetch_assoc($result)['gradeNum'];
								echo "<p class='stats'>${row}</p>"
							?>
							<p>Graded Papers</p>
						</div>
					</div>
					<div class="bottom_stat_summary">
						<div class="numbers_stat_summary">
							<?php
								$sql_query = "SELECT post_id FROM posts WHERE author_id = {$_GET['UserId']};";
								$result = mysqli_query($conn, $sql_query);
								$posts = array();
								while ($row = mysqli_fetch_row($result)) {
									$posts[] = $row;
								}
								$posts = array_column($posts, 0);
								
								$mean = array();
								foreach($posts as $post){
									$sql_query = "SELECT AVG(scale_id_thought_quality),
									AVG(scale_id_connection_response),AVG(scale_id_idea_organization),AVG(scale_id_language_accuracy),AVG(scale_id_references_citations)
									FROM post_ratings WHERE post_id = $post;";
									$result = mysqli_query($conn, $sql_query);
									$row = mysqli_fetch_row($result);
									$row = array_sum($row) / count($row);
									$mean[]=$row;
								}
								if (count($mean)>0){
									$mean = round((array_sum($mean) / count($mean)),2);
									echo "<p class='stats'>${mean}</p>";
								}else{
									echo "<p class='stats'>0</p>";
								}
							?>
							<p>Average Score</p>
						</div>
						<div class="numbers_stat_summary">
						<?php
								$sql_query = "SELECT post_id FROM posts WHERE author_id = {$_GET['UserId']};";
								$result = mysqli_query($conn, $sql_query);
								$posts = array();
								while ($row = mysqli_fetch_row($result)) {
									$posts[] = $row;
								}
								$posts = array_column($posts, 0);
								if (count($posts)>0){
									$posts = implode(',', $posts);
									$sql_query = "SELECT interest_id, MAX(count_criteria) AS max_count
										FROM (
											SELECT post_id, interest_id, COUNT(interest_id) as count_criteria
											FROM post_interests
											WHERE post_id IN ($posts)
											GROUP BY interest_id
										) AS something;";
									$result = mysqli_query($conn, $sql_query);
									$row = mysqli_fetch_row($result);
									$interest_id = $row[0];
									$quantity = $row[1];
									$sql_query = "SELECT interest_name FROM interests WHERE interest_id = $interest_id;";
									$result = mysqli_query($conn, $sql_query);
									$row = mysqli_fetch_row($result)[0];
									
									echo "<p class='stats'>$row ($quantity)</p>";
								}else{
									echo "<p class='stats'>None (0)</p>";
								}
							?>
							<p>Top Topic</p>
						</div>
					</div>
				</div>
				<div>
					<div class="quick_description">
					<?php echo htmlspecialchars($user['profile_descriptions']); ?>
					</div>
				</div>
			</div>
			<div class="statisticss">
				<div class='prime_competencies'>
					<div class="top5_tags_user"> <!-- Top 5 Tags -->
						<p class="Title">Top Tags</p>
						<?php 
							$sql_query = "SELECT post_id FROM posts WHERE author_id = {$_GET['UserId']};";
							$result = mysqli_query($conn, $sql_query);
							$posts = array();
							while ($row = mysqli_fetch_row($result)) {
								$posts[] = $row;
							}
							$posts = array_column($posts, 0);
							if (count($posts)>0){
								$posts = implode(',', $posts);
								$sql_query = "SELECT
								interests.interest_name,
								interests.interest_id, 
								(AVG(post_ratings.scale_id_thought_quality) + 
								AVG(post_ratings.scale_id_connection_response) + 
								AVG(post_ratings.scale_id_idea_organization) + 
								AVG(post_ratings.scale_id_language_accuracy) + 
								AVG(post_ratings.scale_id_references_citations)) / 5 as avg_score
								FROM post_ratings,post_interests,interests 
								WHERE post_ratings.post_id IN ($posts) 
								AND post_ratings.post_id = post_interests.post_id 
								AND post_interests.interest_id = interests.interest_id 
								GROUP BY post_interests.interest_id ORDER BY `avg_score` DESC
								LIMIT 5;";

								$result = mysqli_query($conn, $sql_query);
								$topic_scores = array();
								while ($row = mysqli_fetch_row($result)) {
									$topic_scores[] = $row;
								}
								$interestIDs = array_column($topic_scores, 1);
								foreach($interestIDs as $ind => $id_interest){
									$sql_query = "SELECT COUNT(*) 
									as count FROM post_interests
									WHERE interest_id = $id_interest;";
									$result = mysqli_query($conn, $sql_query);
									$row = mysqli_fetch_row($result);
									$topic_scores[$ind][2] = round($topic_scores[$ind][2],2);
									$topic_scores[$ind][1] = $row[0];
								}
								if(count($topic_scores)>0){
									foreach ($topic_scores as $topic_score) {
										echo '<div class = "top5_topics">';
												echo '<div>';
													echo "<button class = 'topic_btn'>${topic_score[0]}</button>";
												echo '</div>';
												echo '<div class = "rating_topics">';
													echo "<p><span>${topic_score[1]}</span> Posts</p>";
													echo "<p><span>${topic_score[2]}</span> Average Rating</p>";
												echo '</div>';
											echo '</div>';
										}	
								}else{
									echo '<div class = "top5_topics">';
										echo "<button class = 'topic_btn'>Not Available</button>";
									echo '</div>';
								}
							}else{
								echo '<div class = "top5_topics">';
									echo "<button class = 'topic_btn'>Not Available</button>";
								echo '</div>';
							}
						?>
					</div>
					<div> <!-- Strengths -->
						<p class="Title">Writing Competency</p>
						<?php 
							$sql_query = "SELECT post_id FROM posts WHERE author_id = {$_GET['UserId']};";
							$result = mysqli_query($conn, $sql_query);
							$posts = array();
							while ($row = mysqli_fetch_row($result)) {
								$posts[] = $row;
							}
							$posts = array_column($posts, 0);
							if(count($posts)>0){
								$posts = implode(',', $posts);

								$sql_query = "SELECT AVG(scale_id_thought_quality),
								AVG(scale_id_connection_response),
								AVG(scale_id_idea_organization),
								AVG(scale_id_language_accuracy),
								AVG(scale_id_references_citations)
								FROM post_ratings WHERE post_id in ($posts);";
								$result = mysqli_query($conn,$sql_query);
								$row = array_map('floatval',mysqli_fetch_row($result));
								$topic_scores = array(
													array("Quality and Clarity of thoughts, Development of ideas",round($row[0],2)),
													array("Connection and Response",round($row[1],2)),
													array("Organization and Development of Ideas",round($row[2],2)),
													array("Spelling and Grammar",round($row[3],2)),
													array("Reference and Citation",round($row[4],2))
													);
								
							}else{
								$topic_scores = array(
									array("Quality and Clarity of thoughts, Development of ideas",0),
									array("Connection and Response",0),
									array("Organization and Development of Ideas",0),
									array("Spelling and Grammar",0),
									array("Reference and Citation",0)
									);
							}
							foreach ($topic_scores as $topic_score) {
								echo '<div class = "top5_topics">';
									echo '<div>';
										echo "<button class = 'topic_btn'>${topic_score[0]}</button>";
									echo '</div>';
									echo '<div class = "rating_topics">';
										echo "<p><span>${topic_score[1]}</span> Average Rating</p>";
									echo '</div>';
								echo '</div>';
							}
						?>
					</div>
				</div>
				<div id='paper_top_paper' class="paper_top_paper"> <!-- Top Papers -->
					<div id='incription_types'>
						<button onclick='updateType(0)' class='chosen_inscription'>Liked Inscriptions</button>
						<button onclick='updateType(1)' class='chosen_inscription selected_inscription'>Public Inscriptions</button>
						<?php
							$view_profileID = $_GET['UserId'];
							if ($view_profileID == $_SESSION['user_id']){
								echo "<button onclick='updateType(2)' class='chosen_inscription'>Private Inscriptions</button>";
							}
						?>
					</div>
					<div class="all_filters">
						<div class="search_filter_container">
							<input id="searchBar" type="text" name="search" class="search_filter">
							<button onclick='searchFor()' class='search_it_btn'>search</button>
						</div>
						<div class="search_sort_div">
							<div class="filters_check">
								<p>Search for: </p>
								<?php 
									$filters = array('Title','Author','Tags');
									foreach($filters as $filter){
										echo '<div class="checkbox_filter">';
											echo "<input id='${filter}' type='checkbox' name='${filter}'/>";
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
											echo "<input id=${filter} type='radio' name='sortBy' value='${filter}'/>";
											echo "<label>${filter}</label>";
										// echo '</div>';
									}
								?>
								</form>
							</div>
						</div>
					</div>
					<div class="paper_top_paper" id='papers_location'>
					</div>
				</div>
			</div>
		</div>
		<?php
			echo "<script>initializeFollowStatus($initialFollow);</script>";
		?>
</body>
</html>