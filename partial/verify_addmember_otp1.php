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

print_r($_GET); // Prints the array content in a human-readable format


// $projectId = isset($_GET['project_id']) ? $_GET['project_id'] : null;

// echo $projectId;

?>

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $receivedOtp = $_POST['otp'];
// }
// $user_id = $_SESSION['user_id'];

// // Fetching the loggedin user's email
// $user_email = get_user_data($user_id)['email'];


// if ($projectId !== null) {
//     // Your existing code related to fetching the email and OTP

//     // Fetch stored OTP from the database based on project and email
//     $sql = "SELECT * FROM ProjectInvitations WHERE project_id = '$projectId' AND otp = '$receivedOtp' AND is_used = '0'";
//     $result = $connection->query($sql);

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();

//         $fetch_email = $row['email'];
//         echo $fetch_email;



//         // $storedOtpFromDatabase = $row["otp"];

//         // // Compare received OTP with stored OTP
//         // if ($receivedOtp === $storedOtpFromDatabase) {
//         //     // Successful OTP verification
//         //     // Display login page or proceed with login

//         //     // After successful login, display OTP verification page
//         //     if ($_POST['enteredOtp'] === $storedOtpFromDatabase) {
//         //         // Successful OTP validation
//         //         // Update the used_at timestamp to mark the invitation as used
//         //         $updateSql = "UPDATE ProjectInvitations SET used_at = NOW() WHERE project_id = '$projectId' AND email = '$email'";
//         //         $connection->query($updateSql);

//         //         // Add the user to the project with the appropriate role
//         //         // Perform necessary database operations to add user to ProjectUsers table
//         //         // Redirect user to a success page or project dashboard
//         //     } else {
//         //         // Invalid OTP
//         //         // Display error message
//         //     }
//         // } else {
//         //     // Invalid OTP
//         //     // Display error message
//         // }
//     } else {
//         // OTP not found in the database or invitation already used
//         // Display error message or take appropriate action
//     }

// } else {
//     echo "Project ID is not defined.";
// }
// // Close the database connection
// $connection->close();
// ?>