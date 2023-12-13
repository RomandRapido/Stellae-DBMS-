<?php
session_start();
if (isset($_SESSION['user_id'])){
    header("Location: feed/home_page.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" type="text/css" href="modern_signup.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
	<div class="header">
		<p id="stellae">Stellae</p>
		<p class="team_name">Heavenly Bodies</p>
	</div>
	<div class="superior_div">
		<div class="right_div">
			<div class="right_child">
				<div>
					<p id="welcome_new_user">Welcome New User to Stellae!</p>
					<div class="logIn_redirect">
						<p style="font-size: 15px;" id="existing_acc">Already have an account?</p>
						<button class="redirect" onclick="window.location.href='../logIn/log_in_page.html'" id="log_in_button">Log In</button>
					</div>
				</div>
			</div>
		</div>
		<div class="left_div">
			<form method="POST" action="php_modal_connection/addNew_userData.php" id="update_del_form" class="form_signUp" onsubmit="return validate_inputs(['name_0','name_1','user_0'],['user_1','user_2'])">
				<fieldset class="user_table"><!-- FullName -->
					<?php
						$fullname_schema = array(array('text','First Name','f_name'),
												array('text','Last Name','l_name'),
												);
						foreach($fullname_schema as $ind => $user_attribute){
							echo "<fieldset class='db_users'>";
								echo"<label class='placeholder'>$user_attribute[1]</label>";
								echo "<input id='name_$ind' name = '$user_attribute[2]' class='inputing' type='$user_attribute[0]' required>";
							echo "</fieldset>";
						}
					?>
				</fieldset>
				<fieldset class="user_table"><!-- User -->
					<?php
						$user_schema = array(array('text','User Name','username'),
												array('email','Email','email'),
												array('password','Password','pass_word')
												);
						foreach($user_schema as $ind => $user_attribute){
							echo "<fieldset class='db_users'>";
								echo"<label class='placeholder'>$user_attribute[1]</label>";
								echo "<input id='user_$ind' name = '$user_attribute[2]' class='inputing' type='$user_attribute[0]' required>";
							echo "</fieldset>";
						}
					?>
				</fieldset>
				<fieldset class="user_table"><!-- Academic -->
					<fieldset>
						<fieldset class='db_users'>
							<label class='placeholder'>Program</label>
							<input id='program_0' name = 'program' class='inputing' type='text'>
						</fieldset>
						<select name="education" class="selection" required>
							<option value=1>High School</option>
							<option value=2>Undergraduate</option>
							<option value=3>Graduate</option> 
							<option value=4>Masteral</option> 
							<option value=5>PHD</option> 
						</select>
					</fieldset>
					<fieldset>
						<fieldset class='db_users'>
							<label class="placeholder">School</label>
							<input id="school_0" class="inputing" type="text" name="school" oninput="parse_education('container_schools')" required>
						</fieldset>
						<fieldset id="container_schools"></fieldset>
					</fieldset>
				</fieldset>
				<fieldset class="user_table"><!-- Personal Descript -->
					<fieldset class='db_users'>
						<label class="placeholder">Interests</label>
						<input id="inputing" oninput="parse_interest('interest_here')" class="inputing" type="text" name="interests" required>
					</fieldset>
					<fieldset id="chosen_interest"></fieldset>
					<fieldset id="interest_here"></fieldset>
					<textarea name="description" class="text_area" placeholder="Description Section" maxlength="200"></textarea>
				</fieldset>
				<fieldset>
					<fieldset style="margin-bottom: 10px;">
						<input id="user_agreed" type="checkbox" name="agreed" required>
						<label>I agree with the Terms and Conditions</label>
					</fieldset>
					<input type="submit" name="submit" value="Sign Up">
				</fieldset>
			</form>
		</div>
	</div>
	<script type="text/javascript" src="modern_signup.js"></script>
</body>
</html>