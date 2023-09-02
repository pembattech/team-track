<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($recipientEmail, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'test2k1209@gmail.com';
        $mail->Password = 'ghryuhkvbitzfqjc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('test2k1209@gmail.com', 'Team Track');
        $mail->addAddress($recipientEmail, '');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        echo '';
        return true;
    } catch (Exception $e) {
        echo '' . $mail->ErrorInfo;
        return false;
    }
}
?>