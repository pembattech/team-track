<?php
// Load database connection and necessary functions
require_once 'config/connect.php'; // Adjust the path to your connection file

// Get the entered OTP from AJAX POST
$enteredOtp = $_POST['otp'];

// Fetch stored OTP from the database based on your table structure
// Adjust the SQL query accordingly
$sql = "SELECT otp FROM ProjectInvitations WHERE project_id = '$projectId' AND email = '$email'";
$result = mysqli_query($connection, $sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $storedOtpFromDatabase = $row["otp"];

    if ($enteredOtp === $storedOtpFromDatabase) {
        // Perform the necessary actions for successful verification
        echo "OTP verified successfully!";
    } else {
        echo "Invalid OTP";
    }
} else {
    echo "OTP not found";
}

// Close the database connection
mysqli_close($connection);
?>

