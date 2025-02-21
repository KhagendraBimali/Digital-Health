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

$hospitalID = $_SESSION['hospital_id'];


$stmt = $conn->prepare("SELECT full_name FROM admin_signup WHERE hospital_id=?");
$stmt->bind_param('s', $hospitalID);
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
