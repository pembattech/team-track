    <?php
    require_once '../config/connect.php';

    session_start();

    // Check if the form is submitted
    if (isset($_POST["submit"])) {
        $user_id = $_SESSION['user_id'];
        $about = $_POST["about"];
        $profile_picture = $_FILES["new-profilepic"];

        echo $profile_picture;

        
        if ($profile_picture !== null && $profile_picture === "Array" && $profile_picture !== "" ) {
            echo "Hi";
            // Define the directory where profile pictures will be stored
            $targetDir = "/static/image/profile_pictures/";

            echo $targetDir;

            // Create the directory if it doesn't exist
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Get the file details
            $file = $_FILES["new-profilepic"];
            $fileName = basename($file["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            echo $file;
            echo $file['tmp_name'];

            // Check if the file was uploaded successfully
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {

                // Get the user ID of the logged-in user
                $user_id = $_SESSION['user_id'];
                $profile_picture_path = $targetFilePath;

                $update_profile_query = "UPDATE Users SET about = '$about', profile_picture = '$profile_picture_path' WHERE user_id = $user_id";

                // Execute the query
                if ($connection->query($update_profile_query) === TRUE) {
                    echo "Profile information updated successfully!";
                    $_SESSION['notification_message'] = "Profile information update successfully.";

                } else {
                    echo "Error updating profile information: " . $connection->error;
                    $_SESSION['notification_message'] = "Error updating profile information.";
                    echo "Sorry, there was an error uploading your file.";
                }

            }
        } else {
            echo "helo";
            // SQL query to update the about information for the user
            $update_profile_query = "UPDATE Users SET about = '$about' WHERE user_id = $user_id";

            // Execute the query
            if ($connection->query($update_profile_query) === TRUE) {
                echo "Profile information updated successfully!";
                $_SESSION['notification_message'] = "Profile information update successfully.";

            } else {
                echo "Error updating profile information: " . $connection->error;
                $_SESSION['notification_message'] = "Error updating profile information.";
            }
        }

        // header("Location: ../profile.php");
    }

    ?>