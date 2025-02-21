<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
$email = $_SESSION['email'];

if (isset($_SESSION['otp']) === false) {
    $otp = mt_rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    $full_name = $_SESSION['full_name'];

    try {
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USERNAME');
        $mail->Password = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = getenv('SMTP_SECURE');
        $mail->Port = getenv('SMTP_PORT');

        $mail->setFrom(getenv('SMTP_FROM_EMAIL'), getenv('SMTP_FROM_NAME'));
        $mail->addAddress($email, $full_name);

        $mail->isHTML(true);
        $mail->Subject = 'Verification';
        $message = 'Your OTP for email verification is: ' . $otp;
        $mail->Body = $message;

        $mail->send();

        echo '<h5>Verification OTP has been sent to <b>' . $email . '</b><h5>';
    } catch (Exception $e) {
        echo "<br><br>Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $entered_otp = $_POST['otp'];

    
    $stored_otp = $_SESSION['otp'];

    
    if ($entered_otp == $stored_otp) {
        

        
        
        
        
        if (isset($_SESSION['signup'])&&$_SESSION['signup']===true) {
            unset($_SESSION['otp']);
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $database = 'hospital';
            
            
            $conn = new mysqli($host, $username, $password, $database);
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $query = "UPDATE patients_signup SET status='active' WHERE email=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
            
            unset($_SESSION);
            header('Location: patient_login.php');
            exit();
        } 
        else {
            unset($_SESSION['otp']);
            header("Location: reset_password.php");
            exit();
            
        }
    
} 
else {
    
    echo 'Incorrect OTP. Please try again.';
    exit();
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="patient_signup.css">>
</head>
<body>
    <div>
        <?php include('header.html');?>

    </div>
    <div class='signup-container'>
            <div class="signup-box">
        <form method="POST" action="">
            <label for="otp">Enter OTP:</label>
            <input type="text" name="otp" required>
            <button type="submit">Verify</button>
        </form>
        </div>
    </div>
    <footer>
    <?php include "footer.html" ?>
    </footer>
</body>
</html>
