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

$doctorID = $_SESSION['doctor_id'];


$stmt = $conn->prepare("SELECT full_name FROM doctor_signup WHERE doctor_id=?");
$stmt->bind_param('s', $doctorID);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["full_name"];
    echo json_encode(array("name" => $name));
} else {
    echo "No name found";
}
$stmt->close();
$conn->close();
?>
