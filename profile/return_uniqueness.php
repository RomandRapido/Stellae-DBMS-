<?php
	$dbServer = 'localhost';
	$dbUserName = 'Romand';
	$dbPass = 'Romand';
	$dbName = 'stellae';

	$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);
	function return_bool(){
		$data = isset($_POST['data']) ? $_POST['data'] : '';
		$data = json_decode($data);

		$sql_query1 = "SELECT email FROM users WHERE email = '$data[1]';";
		$result1 = mysqli_query($conn,$sql_query);

		$sql_query2= "SELECT user_name FROM users WHERE user_name = '$data[0]';";
		$result2 = mysqli_query($conn,$sql_query);

		return json_encode(['finding1'=> $result1,'finding2'=> $result2]);
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$action = isset($_POST['action']) ? $_POST['action'] : '';
		    switch ($action) {
		    	case 'return_bool':
		    		echo return_bool();
		    		break;
		    	default:
		    		break;
		    }
		}
?>