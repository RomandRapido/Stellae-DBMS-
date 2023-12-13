<?php
	function interest_new(){
			$dbServer = 'localhost';
			$dbUserName = 'Romand';
			$dbPass = 'Romand';
			$dbName = 'stellae';

			$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);
			$data = isset($_POST['arrayVal']) ? $_POST['arrayVal'] : '';

			$buttons = array();
			foreach($data as $datum){
				$sql_query = "SELECT interest_name FROM interests WHERE interest_id = ${datum};";
				$result = mysqli_query($conn,$sql_query);
				$interest = mysqli_fetch_assoc($result);
				$buttons[]=rtrim($interest['interest_name'], "\r");
			}
			echo json_encode($buttons);
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$action = isset($_POST['action']) ? $_POST['action'] : '';
			    switch ($action) {
			    	case 'interest_new':
			    		interest_new();
			    		break;
			    	default:
			    		break;
			    }
			}
?>