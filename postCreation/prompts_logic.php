<?php
$servername = 'localhost';
$username = 'Romand';
$password = 'Romand';
$dbname = 'stellae' ;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$difficulty = $_GET['difficulty'];

$sql = "SELECT question FROM prompts WHERE difficulty_level = ? ORDER BY RAND() LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $difficulty);
$stmt->execute();
$stmt->bind_result($question);
$stmt->fetch();
$stmt->close();

$conn->close();

echo $question;
?>
