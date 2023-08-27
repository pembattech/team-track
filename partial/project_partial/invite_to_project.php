<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include '../../partial/send_mail.php'; ?>
<?php include '../../partial/gen_otp.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $message = $_POST["message"];
    $project_id = $_POST["project_id"];

    echo $email;
    echo $message;
    // Backend validation
    if (empty($email)) {
        echo "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address format.";
    } else {

        // Generate a new unique OTP
        $uniqueOtp = generateUniqueOTP();

        // SQL query to insert data into ProjectInvitations table
        $sql = "INSERT INTO ProjectInvitations (project_id, email, otp) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iss", $project_id, $email, $uniqueOtp);

        if ($stmt->execute()) {
            echo "Data inserted successfully!";
        } else {
            echo "Error inserting data: " . $stmt->error;
        }


        // Sending the email
        $subject = "Invitation to Join";
        $to = $email;
        $mailMessage = "You have received an invitation to join the <a href='http://localhost/teamtrack/project.php?project_id=" . $project_id . "'>project</a>.\n\n Your verification PIN is " . $uniqueOtp;
        if (!empty($message)) {
            $mailMessage .= "Message: $message\n";
        }

        if (sendEmail($to, $subject, $mailMessage)) {
            echo "Invitation sent successfully!";
        } else {
            echo "Failed to send invitation.";
        }
    }
} else {
    echo "Invalid request.";
}
?>