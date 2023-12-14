<?php
	session_start();
	$dbServer = 'localhost';
	$dbUserName = 'Romand';
	$dbPass = 'Romand';
	$dbName = 'stellae';

	$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);
	if (!$conn){die("Cannot connect to database");}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST['submit'])){
			$f_name = ucwords(trim($_POST['f_name']));
			$l_name = ucwords(trim($_POST['l_name']));
			$email = trim($_POST['email']);
			$username = trim($_POST['username']);
			$password_mo = password_hash($_POST['pass_word'], PASSWORD_DEFAULT);
			$education = ucwords(trim($_POST['education']));
			$school = trim($_POST['school']);
			$description = trim($_POST['description']);
			$program = trim($_POST['program']);
			$interests = $_POST['interests'];

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
			

			$userUpdate = array($f_name,$l_name,$email,$username,$password_mo,
								$education,$school,$description,$program);
			// echo '<pre>';
			// 	print_r($userUpdate);
			// echo '</pre>';
			$array_columns = array('first_name','last_name','email','user_name','passwordHash','education_level_id','school_id','profile_descriptions','program');

			$array_columns = implode(',', $array_columns);

			$queryNew = "INSERT INTO users (first_name, last_name, email, user_name, password_hash, education_level_id, school_id, profile_descriptions, program) VALUES ('$userUpdate[0]', '$userUpdate[1]', '$userUpdate[2]', '$userUpdate[3]', '$userUpdate[4]', $userUpdate[5], $userUpdate[6], '$userUpdate[7]', '$userUpdate[8]')";
			$resultNew = mysqli_query($conn, $queryNew);
			if (!$resultNew){die("Insert user info failed");}
			$insertedPrimaryKey = mysqli_insert_id($conn);
			if($interests){
				$interests = explode(",", $interests);
				foreach($interests as $int_) {
				    $queryNew = "INSERT INTO user_interests (user_id,interest_id) VALUES ($insertedPrimaryKey, $int_)";
				    $resultNew = mysqli_query($conn, $queryNew);
				    if (!$resultNew) {
				        die("Insert user interests failed");
				    }
				}
			}
		}
		echo "<script>alert('Registration Successful'); window.location.href='../../login/log_in_page.html';</script>";
		exit();
		
	}

?>