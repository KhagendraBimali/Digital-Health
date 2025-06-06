<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "hospital") or die("connection failed");

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM admin_signup WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $count = mysqli_num_rows($result);
    
    if ($count == 1) {
        while($row=mysqli_fetch_assoc($result)){
            
            if(password_verify($password, $row['password'])){
                $_SESSION['hospital_id']= $row['hospital_id'];
                $_SESSION['email'] = $row['email'];
                header("Location: /project/hospital-dashboard/dashboard.html");
                exit;
            }
            else{

                $error_message = "Invalid Email or Password";

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
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" type="text/css" href="../patient_signup.css">
</head>
<body>
    <?php include "../header.html"?>
    <div class="signup-container">
        <div class="left-section">
            <img src="../pic/logod.png" alt="Your Logo">
        </div>
        <div class="right-section">
            <div class="signup-box">
                <form method="post" action="admin_login.php">
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
                <h5>Don't have an account? <a href="admin_signup.php">Click here to signup</a></h5>
                <h5><a href="forget_password.php">Forget Password</a></h5>

            </div>
        </div>
    </div>
    <?php include "../footer.html"?>
</body>
</html>
