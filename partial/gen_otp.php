<?php
// Replace with your actual database credentials
$connection = new mysqli("localhost", "root", "", "teamtrack");

// Check for database connection error
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Function to generate a unique OTP
function generateUniqueOTP() {
    global $connection;
    do {
        $otp = random_int(100000, 999999); // Generate a 6-digit OTP
        $checkSql = "SELECT COUNT(*) AS count FROM ProjectInvitations WHERE otp = '$otp'";
        $checkResult = $connection->query($checkSql);
        $row = $checkResult->fetch_assoc();
    } while ($row["count"] > 0);

    return $otp;
}

?>

