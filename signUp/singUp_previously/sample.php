<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get JSON data from the request
$jsonData = file_get_contents("php://input");

// Decode JSON data
$phpArray = json_decode($jsonData, true);
if (
    !isset($phpArray['data'][0][0], $phpArray['data'][0][1],
            $phpArray['data'][1], $phpArray['data'][2],
            $phpArray['data'][3], $phpArray['data'][5])
) {
    die("Error: Required fields are missing");
}

$servername = "localhost";
$username = "root_mo";
$password = "";
$dbname = "stellae";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tableName = "users";

$preparedData = $phpArray['data'];

$preparedData['name'][0] = trim(ucwords(strtolower($conn->real_escape_string($preparedData[0][0]))));
$preparedData['name'][1] = trim(ucwords(strtolower($conn->real_escape_string($preparedData[0][1]))));
$preparedData['email'] = $conn->real_escape_string($preparedData[1]);
$preparedData['user_name'] = $conn->real_escape_string($preparedData[2]);
$preparedData['password'] = password_hash($preparedData[3], PASSWORD_DEFAULT);
$preparedData['school'] = trim(ucwords(strtolower($conn->real_escape_string($preparedData[5]))));

$findSchoolIfExistsQuery = "SELECT school_id FROM school_options WHERE LOWER(school_name) = LOWER('{$preparedData['school']}')";
$findSchoolIfExistsResult = mysqli_query($conn, $findSchoolIfExistsQuery);
if ($findSchoolIfExistsResult) {
    if (mysqli_num_rows($findSchoolIfExistsResult) > 0) {
        $school = mysqli_fetch_assoc($findSchoolIfExistsResult);
        $schoolId = $school['school_id'];
    } else {
        $createNewSchoolQuery = "INSERT INTO school_options (school_name) VALUES ('{$preparedData['school']}')";
        $createNewSchoolResult = mysqli_query($conn, $createNewSchoolQuery);
        if (!$createNewSchoolResult) { die("Adding school query failed: " . mysqli_error($conn)); }
        $schoolId = mysqli_insert_id($conn);
    }
} else {
    die("Error querying for school: " . mysqli_error($conn));
}

$sql = "INSERT INTO $tableName (last_name, first_name, email, user_name, password_hash, school_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssssss", $preparedData['name'][1], $preparedData['name'][0], $preparedData['email'], $preparedData['user_name'], $preparedData['password'], $schoolId);
    if ($stmt->execute()) {
        echo "Record inserted successfully";
        $userId = mysqli_insert_id($conn);
    } else {
        echo "Error inserting user: " . $stmt->error;
    }
    $stmt->close();
} else {
    die("Error preparing user insertion: " . $conn->error);
}

if ($userId) {
    foreach ($preparedData[6] as $interestName) {
        $interestName = trim(ucwords(strtolower($conn->real_escape_string($interestName))));
        $findInterestQuery = "SELECT interest_id FROM interests WHERE LOWER(interest_name) = LOWER('$interestName')";
        $findInterestResult = mysqli_query($conn, $findInterestQuery);
        if ($findInterestResult) {
            if (mysqli_num_rows($findInterestResult) > 0) {
                $interest = mysqli_fetch_assoc($findInterestResult);
                $interestId = $interest['interest_id'];
            } else {
                $createInterestQuery = "INSERT INTO interests (interest_name) VALUES ('$interestName')";
                $createInterestResult = mysqli_query($conn, $createInterestQuery);
                if (!$createInterestResult) {
                    die("Error creating interest: " . mysqli_error($conn));
                }
                $interestId = mysqli_insert_id($conn);
            }
        } else {
            die("Error querying for interest: " . mysqli_error($conn));
        }
        $insertUserInterestQuery = "INSERT INTO user_interests (user_id, interest_id) VALUES (?, ?)";
        $stmtUserInterest = $conn->prepare($insertUserInterestQuery);
        if ($stmtUserInterest) {
            $stmtUserInterest->bind_param("ii", $userId, $interestId);
            if (!$stmtUserInterest->execute()) {
                die("Error inserting user interest: " . $stmtUserInterest->error);
            }
            $stmtUserInterest->close();
        } else {
            die("Error preparing user interest insertion: " . $conn->error);
        }
    }
}

$conn->close();
?>
