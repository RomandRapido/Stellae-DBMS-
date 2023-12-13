<?php
	session_start();
	$host = 'localhost';
	$username = 'Romand';
	$password = 'Romand'; 
	$database = 'stellae';
	$conn = mysqli_connect($host, $username, $password, $database);

	if (isset($_GET['postId'])) {
		$postId = $_GET['postId'];
	}else{
		$postId = -1;
	}

	if ($_SESSION['user_id']){
		$loggedIn = true;
	}else{
		header('Location: ../logIn/log_in_page.html');
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" type="text/css" href="post_creation.css">
	<link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />
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
		<p id="stellae">Stellae</p>
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
		<div class="post_creation_div">
			<div class="prompts_and_summary_statistics">
				<div class="information_div">
					<div style="margin-top: 30px;" class="cointain">
						<input id="input_title" class="inputing" type="text" name="" required>
						<p class="placeholder">Post Title</p>
					</div>
					<div class="related_tags_parent">
						<div class="cointain">
							<input id="input_interest" class="inputing" type="text" name="" required>
							<p class="placeholder">Related Tags</p>
						</div>
						<div class="related_tags">
							<?php 
								$interest_array = array('Mathematics',
								'Philosophy',
								'Political Science',
								'Psychology',
								'Biological Science',
								'Contemporary Filipino ',
								'Literature and English',
								'Language Studies'
								);
								foreach ($interest_array as $ind => $interest) {
									$button_id = "interest_id${ind}";
									echo "<button id='${button_id}' onclick=\"toggle_class('${button_id}')\" class='interests_available turn_flame'>${interest}</button>";
								}
								echo "<button id='dot_dot_dot' class='none_or_mores'>&#8226;&#8226;&#8226;</button>";
							?>
						</div>
					</div>
				</div>
				<div class="prompt">
					<div class="random_prompts_div">
						<p class="random_prompts_p">Random Prompts</p>
						<form class="dynamic-form" id="questionForm">
						<select class="select" name="institute" id="institute">
							<option selected="selected" value="Easy">Apprentice Level</option>
							<option value="Medium">Mage Level</option>
							<option value="Hard">Archmage Level</option>  
						</select>
						<button type="button" class="randomize_btn" onclick="getRandomQuestion()" >&#8635;</button>
					</div>
					<div class="prompt_location">
						<p class="prompt_itself" id="promptText">"How does one balance the need for independence with the desire for connection?"</p>	
					</div>
					</form>
				</div>
			</div>
			<form class="dynamic-form" action="testing.php" method="POST">
				<textarea class="text_area_post" name="editor_content" id="myEditor"></textarea>
				<fieldset>
				  	<input type="button" name="public" value="Public">
				  	<input type="button" name="private" value="Private">
			  	</fieldset>
			</form>
		</div>
	</div>
	<script src="post_creation.js"></script>
	<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>
	<script> 
		var editor = new FroalaEditor('#myEditor', {toolbarInline: false});
    </script>
	<?php 
		$checkIfAuthorQuery = "SELECT * FROM `posts` WHERE post_id = $postId";
		$checkIfAuthorResult = mysqli_query($conn, $checkIfAuthorQuery);
		if (!$checkIfAuthorResult) { die("Query for finding author failed."); }
		if (mysqli_num_rows($checkIfAuthorResult) > 0){
			$paper = mysqli_fetch_assoc($checkIfAuthorResult);
			if ($paper['author_id']==$_SESSION['user_id']){
				echo "<script>initializeEditing($postId)</script>";
			}else{
				echo "<script>window.location='post_creation.php';</script>";
			}
		}
	?>
</body>
</html>