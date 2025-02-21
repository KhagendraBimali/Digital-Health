<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "hospital") or die("connection failed");

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM patients_signup WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $count = mysqli_num_rows($result);
    
    if ($count == 1) {
        while($row=mysqli_fetch_assoc($result)){
            if($row["status"] === 'active'){
            if(password_verify($password, $row['password'])){
                $_SESSION['patient_id'] = $row['patient_id'];
                $_SESSION['email'] = $row['email'];
                header("Location: dashboard\dashboard.html");
                exit;
            }
            else{
                $error_message = "Invalid Email or Password";

            }
        }
        else{
            $_SESSION['email'] = $email;
            $_SESSION['signup'] = true;
            echo 'Email not verified';
            header("Location: verify.php");
        }
        }
    } else {
        $error_message = "Invalid Email or Password";
    }
} 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="patient_signup.css">
    
</head>
<body >
<?php include "header.html";?>
    <div class="signup-container">
        <div class="left-section">
            <img src="pic/logo.png" alt="Your Logo">
        </div>
        <div class="right-section">
            <div class="signup-box">
                <form method="post" action="patient_login.php">
                    <h1>Login</h1>
                    <?php
                    if (isset($error_message)) {
                        echo "<p style='color: red;'>$error_message</p>";
                    }
                    ?>
                    <div class="input-container">
                        <input type="text" name="email" placeholder="abc@example.com" required>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" name="save">Log In</button>
                </form>
                <h5>Don't have an account? <a href="patient_signup.php">Click here to signup</a></h5>
                <h5><a href="forget_password.php">Forget Password</a></h5>
            </div>
        </div>
    </div>
    <?php include "footer.html";?>
</body>
</html>

