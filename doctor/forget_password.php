<?php
session_start();

$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "hospital";


$conn = mysqli_connect("localhost", "root", "", "hospital") or die("connection failed");



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $query = "SELECT * FROM doctor_signup WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = mysqli_num_rows($result); 

    if($count==1){
        $row = mysqli_fetch_assoc($result);
        $full_name = $row['full_name'];
        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['signup'] = false; 
        header("Location: verify.php");
        exit();
    }
    else{
        echo "The email provided is not registered.";
        header("Location: patients_login.php");
        exit();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup Form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <form method="post" action="forget_password.php">
                    <h1>Forget Password</h1>
                    <div class="input-container">
                        <input type="email" name="email" placeholder="email" required>
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <footer>
    <?php include "../footer.html" ?>
    </footer>
</body>
</html>