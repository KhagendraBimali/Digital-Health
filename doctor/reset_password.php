<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "hospital") or die("connection failed");
$email= $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $password = $_POST['password'];
    $confirm_password= $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        header("reset_password.php");
        exit;
    }

    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $query = "UPDATE doctor_signup SET password = ? WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $hashed_password, $email);  
    $stmt->execute();
    $stmt->close();
    header('Location: doctor_login.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" type="text/css" href="../patient_signup.css">
    
</head>
<body >
<?php include "../header.html";?>
    <div class="signup-container">
        <?php var_dump($_SESSION);?>
        <div class="left-section">
            <img src="pic/logod.png" alt="Your Logo">
        </div>
        <div class="right-section">
            <div class="signup-box">
                <form method="post" action="">
                        <input type="password" name="password" placeholder="New Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit" name="save">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <footer>
    <?php include "../footer.html";?>
    </footer>    
</body>
</html>

