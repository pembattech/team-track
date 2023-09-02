<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
// Start the session to access user data
session_start();

require_once '../config/connect.php';
include '../partial/utils.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receivedOtp = $_POST['otp'];
    $project_id = $_POST['project_id'];
}

$user_id = $_SESSION['user_id'];

// Fetching the loggedin user's username and email
$username = get_user_data($user_id)['username'];
$user_email = get_user_data($user_id)['email'];
echo $user_email;


if ($project_id !== null) {
    // Your existing code related to fetching the email and OTP

    // Fetch stored OTP from the database based on project and email
    $sql = "SELECT * FROM ProjectInvitations WHERE project_id = '$project_id' AND otp = '$receivedOtp' AND is_used = '0'";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $fetch_email = $row['email'];

        if ($fetch_email == $user_email) {
            // Check if the user already exists in the project
            $check_user_query = "SELECT * FROM ProjectUsers WHERE project_id = '$project_id' AND user_id = '$user_id'";
            $user_result = $connection->query($check_user_query);

            if ($user_result->num_rows > 0) {
                echo $username . " already exists in the project.";
            } else {
                echo "Match OTP";
                // Insert that user to that project
                $insert_projectuser_query = "INSERT INTO ProjectUsers (project_id, user_id, is_projectowner) VALUES ('$project_id', '$user_id', '0')";

                // Execute the query to insert the project user into the database
                if (mysqli_query($connection, $insert_projectuser_query)) {
                    echo "Project user inserted successfully.";

                    // Notify the project owner
                    $project_owner_id = get_project_owner_id($project_id);
                    $project_owner_message = "User '$username' has joined the project.";
                    sendNotificationMessage_project_msg($project_owner_id, $project_owner_message);

                    // Notify the invitation sender
                    $invitation_sender_id = $row['invitation_sender'];
                    $invitation_sender_message = "User '$username' has joined the " . get_project_data($project_id) . "project using your invitation.";
                    sendNotificationMessage_project_msg($invitation_sender_id, $invitation_sender_message);

                    // Update the is_used column to mark the invitation as used
                    $update_used_query = "UPDATE ProjectInvitations SET is_used = 1 WHERE project_id = '$project_id' AND otp = '$receivedOtp' AND is_used = 0";

                    if ($connection->query($update_used_query)) {
                        echo "Invitation verified and marked as used.";

                    } else {
                        echo "Error updating invitation: " . mysqli_error($connection);
                    }

                } else {
                    echo "Error inserting project user: " . mysqli_error($connection);
                }
            }
        } else {
            echo "Invalid OTP or invitation already used.";
        }
    } else {
        echo "Invalid OTP or invitation already used.";
    }

} else {
    echo "Project ID is not defined.";
}
// Close the database connection
$connection->close();
?>