<?php 
    header('Content-Type: application/json');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $host = 'localhost';
        $username = 'Romand';
        $password = 'Romand'; 
        $database = 'stellae';

        $conn = mysqli_connect($host, $username, $password, $database);
        if (!$conn) {
            die ("Cannot connect to the database");
        }

        $postData = json_decode(file_get_contents('php://input'), true);

        $username = trim($postData['username']);
        $email = $postData['email'];

        // Convert email to lowercase for case-insensitive comparison
        $email = trim(strtolower($email));

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

        echo json_encode([
            'uniqueUsername' => ($rowUsername['count'] === 0),
            'uniqueEmail' => ($rowEmail['count'] === 0)
        ]);
        $conn->close();
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
    

?>
