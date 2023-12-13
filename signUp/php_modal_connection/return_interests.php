<?php
	function interest_get(){
		$dbServer = 'localhost';
		$dbUserName = 'Romand';
		$dbPass = 'Romand';
		$dbName = 'stellae';

		$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);
		$data = isset($_POST['value']) ? $_POST['value'] : '';

		$sql_query = "SELECT * FROM interests WHERE interest_name LIKE '%${data}%';";
		$result = mysqli_query($conn,$sql_query);
		$interests = array();

		if(mysqli_num_rows($result) > 0){
			while($interest = mysqli_fetch_assoc($result)){
				$interests[] = $interest;
			}
		}
		echo json_encode($interests);
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$action = isset($_POST['action']) ? $_POST['action'] : '';
		    switch ($action) {
		    	case 'interest_get':
		    		interest_get();
		    		break;
		    	default:
		    		break;
		    }
		}
?>