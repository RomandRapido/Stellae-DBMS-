<?php
$dbServer = 'localhost';
$dbUserName = 'Romand';
$dbPass = 'Romand';
$dbName = 'stellae';

$conn = mysqli_connect($dbServer, $dbUserName, $dbPass, $dbName);

function return_bool()
{
    global $conn;

    $data = isset($_POST['data']) ? json_decode($_POST['data'], true) : '';
    $username = $data[0];
    $email = $data[1];

    $email = strtolower($email);

    $stmtUsername = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE user_name = ?");
    $stmtUsername->bind_param("s", $username);
    $stmtUsername->execute();
    $resultUsername = $stmtUsername->get_result();
    $rowUsername = $resultUsername->fetch_assoc();

    $stmtEmail = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE LOWER(email) = ?");
    $stmtEmail->bind_param("s", $email);
    $stmtEmail->execute();
    $resultEmail = $stmtEmail->get_result();
    $rowEmail = $resultEmail->fetch_assoc();

    return json_encode([
        'finding1' => ($rowEmail['count'] === 0),
        'finding2' => ($rowUsername['count'] === 0),
    ]);
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
