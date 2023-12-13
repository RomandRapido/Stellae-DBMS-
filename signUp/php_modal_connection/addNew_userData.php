<?php

	$dbServer = 'localhost';
	$dbUserName = 'Romand';
	$dbPass = 'Romand';
	$dbName = 'stellae';

	$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);
	if (!$conn){die("Cannot connect to database");}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST['submit'])){
			$f_name = $_POST['f_name'];
			$l_name = $_POST['l_name'];
			$email = $_POST['email'];
			$username = $_POST['username'];
			$password_mo = password_hash($_POST['pass_word'],PASSWORD_DEFAULT);
			$education = $_POST['education'];
			$school = $_POST['school'];
			$description = $_POST['description'];
			$program = $_POST['program'];
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
			if($interests){
				$interests = explode(',',$interests);
				$interests = array_map('intval',$interests);
			}

			$userUpdate = array($f_name,$l_name,$email,$username,$password_mo,
								$education,$school,$description,$program);
			// echo '<pre>';
			// 	print_r($userUpdate);
			// echo '</pre>';
			$array_columns = array('first_name','last_name','email','user_name','passwordHash','education_level_id','school_id','profile_descriptions','program');

			$array_columns = implode(',', $array_columns);

			$queryNew = "INSERT INTO users (first_name, last_name, email, user_name, password_hash, education_level_id, school_id, profile_descriptions, program) 
			VALUES ('$userUpdate[0]', '$userUpdate[1]', '$userUpdate[2]', '$userUpdate[3]', '$userUpdate[4]', $userUpdate[5], $userUpdate[6], '$userUpdate[7]', '$userUpdate[8]')";
			echo $queryNew;
			$resultNew = mysqli_query($conn, $queryNew);

			if (!$resultNew){die("Insert user info failed");}
			echo '<script>alert("Registration Successful")</script>';
			header('Location: ../../logIn/log_in_page.html');
		}
		
	}

?>
