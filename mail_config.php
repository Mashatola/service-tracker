<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendOTPEmail($toEmail, $otp){

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'trackingsystem04@gmail.com';
        $mail->Password = 'oysgjrwqssdwpvrf';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('trackingsystem04@gmail.com', 'Service Tracking System');
        $mail->addAddress($toEmail);

        $mail->Subject = "Your OTP Code";
        $mail->Body = "Your OTP is: $otp";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

