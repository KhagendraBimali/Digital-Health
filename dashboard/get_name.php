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

$patientID = $_SESSION['patient_id'];


$stmt = $conn->prepare("SELECT full_name, profile FROM patients_signup WHERE patient_id=?");
$stmt->bind_param('s', $patientID);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["full_name"];
    $profile = $row['profile'];
    echo json_encode(array("name" => $name, "profile" => $profile));
} else {
    echo "No name found";
}
$stmt->close();
$conn->close();
?>
