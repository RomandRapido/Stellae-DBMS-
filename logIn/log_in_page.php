<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        echo "haeagsa";
        $host = 'localhost';
        $userName = 'Romand';
        $password = 'Romand';
        $dbName = 'stellae';
        
        $conn = new mysqli($host, $userName, $password, $dbName);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $email = strtolower($_POST['email']);
        $passwordInput = $_POST['password'];

        $query = "SELECT user_id, user_name, password_hash FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if ($result->num_rows > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($passwordInput, $user['password_hash'])) {
                $_SESSION['user_id'] = (int)$user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];

                $imageQuery = "SELECT image_dir FROM users
                        WHERE users.user_id = {$_SESSION['user_id']}";

                $imageResult = mysqli_query($conn, $imageQuery);
                if ($imageResult->num_rows > 0) {
                    $imagDir = mysqli_fetch_assoc($imageResult);
                    $_SESSION['image_dir'] = $imagDir['image_dir'];
                } else {
                    $_SESSION['image_dir'] = "imgDirectory/default.jpg";
                }

                echo '<script>alert("Login successful!"); window.location.href="../feed/home_page.php";</script>';
            } else {
                echo '<script>alert("Wrong credentials. Please check again!"); window.location.href="log_in_page.html";</script>';
            }
        } else {
            echo '<script>alert("Wrong credentials. Please check again!"); window.location.href="log_in_page.html";</script>';
        }

        $conn->close();
    }
?>
