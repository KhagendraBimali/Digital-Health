<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $patient_id = $_SESSION['patient_id'];
    $message = $_POST["message"];
    $doctorId = $_POST["doctor_id"];

    $time = date('Y-m-d H:i:s');

    
    $sql = "INSERT INTO messages (patient_id, time, message, type, doctor_id) VALUES (?, ?, ?, ?, ?)";

    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $patient_id, $time, $message, $type, $doctorId);
    
    $type = "patient"; 
    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Method not allowed.";
}
?>

