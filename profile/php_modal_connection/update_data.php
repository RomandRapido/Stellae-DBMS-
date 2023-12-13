<?php
	
	session_start();
	$dbServer = 'localhost';
	$dbUserName = 'Romand';
	$dbPass = 'Romand';
	$dbName = 'stellae';

	$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST['update_submit'])){
			$f_name = $_POST['f_name'];
			$l_name = $_POST['l_name'];
			$email = $_POST['email'];
			$username = $_POST['username'];
			$password_mo = $_POST['password_mo'];
			$education = $_POST['education'];
			$school = $_POST['school'];
			$description = $_POST['description'];
			$program = $_POST['program'];
			$interests = $_POST['interests'];
			echo '<pre>';
			print_r($school);
			echo '</pre>';
			if ($school) {
				if (is_numeric($school)) {
					$school = intval($school);
				} else {
					$queryKo = "INSERT INTO school_options (school_name) VALUES ('$school')";
					$resultMo = mysqli_query($conn, $queryKo);  // Fix variable name here
					$queryKo = "SELECT MAX(school_id) FROM school_options";
					$resultMo = mysqli_query($conn, $queryKo);  // Fix variable name here
					$row = mysqli_fetch_row($resultMo);
					$school = intval($row[0]);
				}				
			}
			if($education){
				$education = intval($education);
			}
			if($interests){
				$interests = explode(',',$interests);
				$interests = array_map('intval',$interests);
			}
			$userUpdate = array($f_name,$l_name,$email,$username,$password_mo,
								$education,$school,$description,$program);
			$array_columns = array('first_name','last_name','email','user_name','password_hash','education_level_id','school_id','profile_descriptions','program');
			foreach($userUpdate as $ind => $userInfo){
				if(!$userInfo){
					$column = $array_columns[$ind];

					$sql_query = "SELECT $column FROM users WHERE user_id = {$_SESSION['user_id']};";
					$result = mysqli_query($conn, $sql_query);
					$row = mysqli_fetch_assoc($result);
					if ($ind == 5 or $ind == 6){
						$row[$column] = intval($row[$column]);
					}
					$userUpdate[$ind] = $row[$column];
				}
			}
			foreach ($userUpdate as $ind => $userInfo) {
				$column = $array_columns[$ind];
				$userInfo = mysqli_real_escape_string($conn, $userInfo); // Sanitize input to prevent SQL injection
				$sql_query = "UPDATE users SET $column = '$userInfo' WHERE user_id = {$_SESSION['user_id']}";
			
				$result = mysqli_query($conn, $sql_query);
				if($ind == 3){
					$_SESSION['user_name'] = $userInfo;
				}
			}

			if($interests){
				$sql_query = "DELETE FROM user_interests WHERE user_id = {$_SESSION['user_id']};";
				$result = mysqli_query($conn,$sql_query);
				foreach($interests as $interest){
					$sql_insert = "INSERT INTO user_interests VALUES({$_SESSION['user_id']},${interest});";
					$result = mysqli_query($conn,$sql_insert);
				}
			}
			if (isset($_FILES['filename']) && $_FILES['filename']['error'] === UPLOAD_ERR_OK) {
				include 'upload.php';
			}else {
				echo 'Error uploading the file: ' . $_FILES['filename']['error'];
			}
			header('Location: ../account_view.php?UserId=' . $_SESSION['user_id']);	
		}elseif (isset($_POST['delete_submit'])){
			mysqli_begin_transaction($conn);
			try {
				$tablesToDeleteFrom = ['followers', 'posts', 'post_comments', 'post_likes', 'post_ratings', 'user_interests'];
				foreach ($tablesToDeleteFrom as $table) {
					$deleteQuery = "DELETE FROM $table WHERE user_id = {$_SESSION['user_id']}";
					mysqli_query($conn, $deleteQuery);
				}

				$deleteUserQuery = "DELETE FROM users WHERE user_id = {$_SESSION['user_id']}";
				mysqli_query($conn, $deleteUserQuery);

				mysqli_commit($conn);
				header('Location: ../../logIn/log_in_page.html');
				echo json_encode(['success' => 'User and related entries deleted successfully']);
			} catch (Exception $e) {
				mysqli_rollback($conn);
				http_response_code(500);
				echo json_encode(['error' => 'Failed to delete user and related entries']);
			}
		}
		
	}

?>