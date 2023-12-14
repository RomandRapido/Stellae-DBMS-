<?php
session_start()
$dbServer = 'localhost';
$dbUserName = 'root_zion';
$dbPass = '';
$dbName = 'stellae';

$conn = mysqli_connect($dbServer, $dbUserName, $dbPass, $dbName);

function get_is_name($conn,$user_name){
    if (!empty(trim($user_name))) {   
        $sql_query = "SELECT user_name FROM users WHERE user_name = ?;";
        $stmt = mysqli_prepare($conn, $sql_query);
        mysqli_stmt_bind_param($stmt, 's', $user_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $checked_a = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $checked_a;
    }else{
        return true;
    }
}
function get_is_email($conn,$user_email){
    if (!empty(trim($user_email))) {
        $sql_query = "SELECT email FROM users WHERE email = ?;";
        $stmt = mysqli_prepare($conn, $sql_query);
        mysqli_stmt_bind_param($stmt, 's', $user_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $checked_b = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $checked_b;
    }else{
        return true;
    }
}
function return_bool($conn) {
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $data = json_decode($data, true); 
    
    $user_name = $data['user_n'];
    $user_email = $data['email_n'];

    $user=get_is_name($conn,$user_name);
    $email = get_is_email($conn,$user_email);

    $result = [];
    $result['user'] = $user;
    $result['email'] = $email;
    $result['status'] = $user || $email;
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    switch ($action) {
        case 'return_bool':
            $result = return_bool($conn);
            echo json_encode(['result' => $result]);
            break;
        default:
            break;
    }
}
?>
