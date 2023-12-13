<?php
	function schools_get(){
		$dbServer = 'localhost';
		$dbUserName = 'Romand';
		$dbPass = 'Romand';
		$dbName = 'stellae';

		$conn = mysqli_connect($dbServer,$dbUserName,$dbPass,$dbName);

		$data = isset($_POST['data']) ? $_POST['data'] : '';

		$sql_query = "SELECT * FROM school_options WHERE school_name LIKE '%${data}%';";
		$result = mysqli_query($conn,$sql_query);
		$schools = array();

		if(mysqli_num_rows($result) > 0){
			while($school = mysqli_fetch_assoc($result)){
				$schools[] = $school;
			}
		}else{
			$schools[] = ['school_id' => $data, 'school_name' => $data];
		}
		echo json_encode($schools);

	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$action = isset($_POST['action']) ? $_POST['action'] : '';
		    switch ($action) {
		    	case 'schools_get':
		    		schools_get();
		    		break;
		    	default:
		    		break;
		    }
		}
?>