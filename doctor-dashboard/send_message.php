<?php
include('db_connection.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST["message"]; 
    $patientId = $_POST["patient_id"];
    
    $doctor_id = $_SESSION['doctor_id'];
    $time = date('Y-m-d H:i:s');

    $sql = "INSERT INTO messages (doctor_id, time, message,type, patient_id) VALUES ('$doctor_id', '$time', '$message','doctor', '$patientId')";

    if ($conn->query($sql) === TRUE) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Method not allowed.";
}
$conn->close();
?>
