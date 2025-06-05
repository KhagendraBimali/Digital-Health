<?php
// Database connection
$host = "localhost"; // Change this to your database server hostname or IP address
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$database = "hospital";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $status = "inactive";
    $profile = "/pic/icon.png";

    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query to insert user data into the database
    $query = "INSERT INTO admin_signup (full_name, address, email, password, status, profile) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $full_name, $address, $email, $hashed_password, $status, $profile); // 'ssss' indicates four string parameters
    
    if ($stmt->execute()) {
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['signup'] = true;
        $stmt->close();
        $conn->close();
        // Redirect to a success page or login page
        header("Location: admin_login.php");
        exit;
    } else {
        // Handle duplicate email or other errors
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup Form</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" type="text/css" href="../patient_signup.css">
    <style>
        .error {
            color: red;
            display: block;
            margin-top: 5px;
        }
    </style>
    <script>
        function validateForm() {
            var fullName = document.getElementsByName('full_name')[0].value;
            var address = document.getElementsByName('address')[0].value;
            var email = document.getElementsByName('email')[0].value;
            var password = document.getElementsByName('password')[0].value;
            var confirmPassword = document.getElementsByName('confirm_password')[0].value;

            var isValid = true;

            // Reset previous errors
            var errors = document.getElementsByClassName('error');
            for (var i = 0; i < errors.length; i++) {
                errors[i].innerText = '';
            }

            // Check if name field is blank
            if (fullName.trim() === '') {
                document.getElementById('full_name_error').innerText = 'Full Name cannot be blank';
                isValid = false;
            }

            // Check if address field is blank
            if (address.trim() === '') {
                document.getElementById('address_error').innerText = 'Address cannot be blank';
                isValid = false;
            }

            // Validate email format (simple format validation)
            var emailRegex = /^\S+@\S+\.\S+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email_error').innerText = 'Please enter a valid email address';
                isValid = false;
            }

            // Check if password field is blank
            if (password.trim() === '') {
                document.getElementById('password_error').innerText = 'Password cannot be blank';
                isValid = false;
            }

            // Check if confirm password matches password
            if (password !== confirmPassword) {
                document.getElementById('confirm_password_error').innerText = 'Passwords do not match';
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>
<body>
<?php include "../header.html" ?>
    <div class="signup-container">
        <div class="left-section">
            <img src="../pic/logod.png" alt="Your Logo">
        </div>
        <div class="right-section">
            <div class="signup-box">
                <form action="admin_signup.php" method="post" onsubmit="return validateForm()">
                    <h1>Create an Account</h1>
                    <div class="input-container">
                        <input type="text" name="full_name" placeholder="Hospital Name" >
                        <span id="full_name_error" class="error"></span>

                        <input type="text" name="address" placeholder="Address" >
                        <span id="address_error" class="error"></span>

                        <input type="text" name="email" placeholder="abc@example.com" >
                        <span id="email_error" class="error"></span>

                        <input type="password" name="password" placeholder="Password" >
                        <span id="password_error" class="error"></span>

                        <input type="password" name="confirm_password" placeholder="Confirm Password" >
                        <span id="confirm_password_error" class="error"></span>
                    </div>
                    <button type="submit">Sign Up</button>
                </form>
                <h5>Already have account <a href="admin_login.php">Click here to login</a></h5>
            </div>
        </div>
    </div>
    <?php include "../footer.html"?>
</body>
</html>
