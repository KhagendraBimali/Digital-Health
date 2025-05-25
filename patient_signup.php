<?php
session_start();


$host = "localhost";
$username = "root";
$password = "";
$database = "hospital";


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $profile = '/project/pic/logo.png';
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit;
    }

    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $stmt = $conn->prepare("SELECT * FROM patients_signup WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        exit;
    }

    
    $stmt = $conn->prepare("INSERT INTO patients_signup (full_name, age, gender, address, email, phone, password, profile) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssss", $full_name, $age, $gender, $address, $email, $phone, $hashed_password, $profile);

    if ($stmt->execute()) {
        
        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['signup'] = true;
        header("Location: patient_login.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup Form</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="patient_signup.css">
    <style>
        .error {
            color: red;
            display: block;
            margin-top: 5px;
        }
    </style>
<script>
    var isValid = true;
    function validateForm() {
        var fullName = document.getElementById('full_name').value;
        var age = document.getElementById('age').value;
        var address = document.getElementById('address').value;
        var email = document.getElementById('email').value;
        var phone = document.getElementById('phone').value;
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirm_password').value;

        
        var errors = document.getElementsByClassName('error');
        for (var i = 0; i < errors.length; i++) {
            errors[i].innerText = '';
        }


        
        if (fullName === '') {
            document.getElementById('full_name_error').innerText = 'Name cannot be blank';
            isValid = false;
        }

        
        if (isNaN(age) || age === '' || parseFloat(age) <= 0) {
            document.getElementById('age_error').innerText = 'Please enter a valid age greater than 1.';
            isValid = false;
        }
        
        if (address === '') {
            document.getElementById('address_error').innerText = 'Address cannot be blank';
            isValid = false;
        }

        
        var emailRegex = /^\S+@\S+\.\S+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email_error').innerText = 'Please enter a valid email address';
            isValid = false;
        } else {
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_email.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText == 'exists') {
                        document.getElementById('email_error').innerText = 'Email already exists';
                        return false
                    } else {
                        
                        document.getElementById('email_error').innerText = '';
                    }
                }
            };
            xhr.send('email=' + encodeURIComponent(email));
            isValid = xhr.onload();
            return isValid;
        }

        
        var phoneRegex = /^\d{10}$/;
        if (!phoneRegex.test(phone)) {
            document.getElementById('phone_error').innerText = 'Phone number must be exactly 10 digits';
            isValid = false;
        }

        
        if (confirmPassword === '') {
            document.getElementById('confirm_password_error').innerText = 'Please enter password';
            isValid = false;
        } else if (password !== confirmPassword) {
            document.getElementById('confirm_password_error').innerText = 'Passwords do not match';
            isValid = false;
        }
        var gender = document.querySelector('input[name="gender"]:checked');
        if (!gender) {
            document.getElementById('gender_error').innerText = 'Please select a gender';
            isValid = false;
        }

        if (!isValid) {
        return false;
    }    }
</script>

</head>
<body>
    <?php include "header.html" ?>
    <div class="signup-container">
        <div class="left-section">
            <img src="pic/logo.png" alt="Your Logo">
        </div>
        <div class="right-section">
            <div class="signup-box">
                <form id="signupForm" action="patient_signup.php" method="POST" onsubmit="return validateForm()">
                    <h1>Create an Account</h1>
                    <div class="input-container">
                        <input type="text" name="full_name" placeholder="Full Name" id="full_name">
                        <span id="full_name_error" class="error"></span>
                        
                        <input type="number" name="age" placeholder="Age" id="age">
                        <span id="age_error" class="error"></span>
                        <label>Gender:</label>
                        <input type="radio" name="gender" value="male"> Male
                        <input type="radio" name="gender" value="female"> Female
                        <span id="gender_error" class="error"></span>

                        <input type="text" name="address" placeholder="Address" id="address">
                        <span id="address_error" class="error"></span>

                        <input type="text" name="email" placeholder="abc@example.com" id="email">
                        <span id="email_error" class="error"></span>

                        <input type="text" name="phone" placeholder="Phone Number" id="phone">
                        <span id="phone_error" class="error"></span>

                        <input type="password" name="password" placeholder="Password" id="password">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password">
                        <span id="confirm_password_error" class="error"></span>
                    </div>
                    <button type="submit">Sign Up</button>
                </form>

                <h5>If you already have an account <a href="patient_login.php">Click here to login</a></h5>
            </div>
        </div>
    </div>
    <?php include "footer.html"?>
</body>
</html>
