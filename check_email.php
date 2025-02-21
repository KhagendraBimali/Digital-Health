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

if(isset($_POST['email'])) {
    $email = $_POST['email'];
    $query = "SELECT * FROM patients_signup WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }
} else {
    echo 'error';
}
?>
