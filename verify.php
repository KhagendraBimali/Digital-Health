<?php
session_start();
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
$email = $_SESSION['email'];
//$full_name = $_SESSION['full_name'];


if (isset($_SESSION['otp'])===false) {
    // Generate a random 6-digit OTP
    $otp = mt_rand(100000, 999999);

    // Store the OTP in the session for later verification
    $_SESSION['otp'] = $otp;

    // Get user details from the session
    //$email = $_SESSION['email'];
    $full_name = $_SESSION['full_name'];

    try {
        // Server settings
        $mail->isSMTP();                                 // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';            // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                        // Enable SMTP authentication
        $mail->Username   = 'gurastechnologies@gmail.com'; // SMTP username
        $mail->Password   = 'BORN@to@BLOOM';          // SMTP password
        $mail->SMTPSecure = 'tls';                       // Enable implicit TLS encryption
        $mail->Port       = 587;                         // TCP port to connect to

        // Recipients
        $mail->setFrom('digitalhealth264@gmail.com', 'Digital Health');
        $mail->addAddress($email, $full_name); // Add a recipient

        // Content
        $mail->isHTML(true);           // Set email format to HTML
        $mail->Subject = 'Verification';
        $message = 'Your OTP for email verification is:' . $otp;
        $mail->Body = $message;

        // Send the email
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->send();


        // Display a message indicating that the OTP has been sent
        echo '<h5>Verification OTP has been sent to <b>' . $email . '</b><h5>';
    } catch (Exception $e) {
        echo "<br><br>Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the entered OTP from the form
    $entered_otp = $_POST['otp'];

    // Get the stored OTP from the session
    $stored_otp = $_SESSION['otp'];

    // Compare entered OTP with stored OTP
    if ($entered_otp == $stored_otp) {
        // OTP verification successful

        // Clear the OTP from the session after successful verification
        
        
        // Determine the redirect URL based on the scenario
        if (isset($_SESSION['signup'])&&$_SESSION['signup']===true) {
            unset($_SESSION['otp']);
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $database = 'hospital';
            
            // Create a database connection
            $conn = new mysqli($host, $username, $password, $database);
            // Check the connection
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
    // Example: Redirect to the determined URL
} 
else {
    // Incorrect OTP
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
    <link rel="stylesheet" type="text/css" href="patient_signup.css">
</head>
<body>
    <div>
        <?php include('header.html');?>

    </div>
    <div class='signup-container'>
            <div class="signup-box">
                <?php //var_dump($_SESSION);?>
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