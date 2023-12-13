<?php
$host = 'localhost';
$username = 'Romand';
$password = 'Romand';
$database = 'stellae';

$conn = mysqli_connect($host, $username, $password, $database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleFormSubmission();
}

function handleFormSubmission() {
    if (!isset($_SESSION['user_id'])) {
        echo "User ID not set in session.";
        return;
    }

    $userId = $_SESSION['user_id'];

    if (isset($_FILES['filename']) && $_FILES['filename']['error'] === UPLOAD_ERR_OK) {
        $uploadDirectoryLocal = '../../imgDirectory/';
        $temp = explode(".", $_FILES["filename"]["name"]);
        $newfilename = $userId . '.' . $temp[1];
        $uploadFile = $uploadDirectoryLocal . $newfilename;
        $uploadDirectoryForDatabase = 'imgDirectory/'.$newfilename;
        
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($imageFileType, $allowedExtensions)) {
            $existingImageQuery = "SELECT * FROM users WHERE user_id = ?";
            $existingImageStmt = mysqli_prepare($GLOBALS['conn'], $existingImageQuery);
            mysqli_stmt_bind_param($existingImageStmt, "s", $userId);
            mysqli_stmt_execute($existingImageStmt);
            mysqli_stmt_store_result($existingImageStmt);

            if (mysqli_stmt_num_rows($existingImageStmt) > 0) {
                foreach ($allowedExtensions as $extension) {
                    $fileToDelete = $uploadDirectoryLocal . $userId . '.' . $extension;
                    if (file_exists($fileToDelete) && !unlink($fileToDelete)) {
                        echo "Error deleting file: $fileToDelete";
                    }
                }

                if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadFile)) {
                    $updateQuery = "UPDATE users SET image_dir = ? WHERE user_id = ?";
                    $updateStmt = mysqli_prepare($GLOBALS['conn'], $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "ss", $uploadDirectoryForDatabase, $userId);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);
                    echo "File uploaded successfully!";
                } else {
                    echo 'Error uploading the file.';
                }
            } else {
                echo "User does not exist.";
            }

            $_SESSION['image_dir'] = $uploadDirectoryForDatabase;

            mysqli_stmt_close($existingImageStmt);
        } else {
            echo 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        }
    } else {
        echo 'No file uploaded or an error occurred during upload.';
    }
}
?>
