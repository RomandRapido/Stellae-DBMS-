<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $UserId = $_POST['UserId'];

    header('Location: Profile/account_view.php?UserId=' . urlencode($UserId));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Username Input</title>
    <!-- Add your CSS styles here if needed -->
</head>
<body>

    <h1>Enter Your Username</h1>

    <!-- Username Input Form -->
    <form action="" method="post">
        <label for="UserId">UserId:</label>
        <input type="text" name="UserId" id="UserId" required>
        <input type="submit" value="Submit">
    </form>

</body>
</html>
