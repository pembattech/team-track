<?php include 'partial/navbar.php'; ?>

<?php
session_start();

// Increase upload file size limit (e.g., 10MB)
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');


// Check if the form is submitted
if (isset($_POST["submit"])) {
    // Define the directory where profile pictures will be stored
    $targetDir = "profile_pictures/";

    // Create the directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Get the file details
    $file = $_FILES["profile_picture"];
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    echo $file["tmp_name"];
    // echo $fileName;
    // echo $targetFilePath;
    // echo $fileType;

    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if (!in_array(strtolower($fileType), $allowedTypes)) {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        exit();
    }

    echo 'hello world';
    // Check if the file was uploaded successfully
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        //     // File was uploaded successfully
        //     // Now you can store the file path in the database for the user
        //     // Assuming you have a database connection established already

        //     // Get the user ID of the logged-in user
        //     $user_id = $_SESSION['user_id'];
        //     $profile_picture_path = $targetFilePath;

        //     // Perform a database query to update the user's profile picture path
        //     // Sample code (make sure to use prepared statements to prevent SQL injection):
        //     /*
        //     $stmt = $pdo->prepare("UPDATE Users SET profile_picture = :profile_picture WHERE user_id = :user_id");
        //     $stmt->bindParam(":profile_picture", $profile_picture_path);
        //     $stmt->bindParam(":user_id", $user_id);
        //     $stmt->execute();
        //     */

        echo "Profile picture uploaded successfully!";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>