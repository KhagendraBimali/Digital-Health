<?php
include('db_connection.php');
$doctorId = $_GET['doctor_id'];
$patientId = $_SESSION['patient_id'];

$sql = "SELECT * FROM messages WHERE doctor_id=? AND patient_id=? ORDER BY time";
$stmt = $conn->prepare($sql);

$stmt->bind_param('ii', $doctorId, $patientId);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messageClass = ($row["type"] === 'patient') ? 'message-right' : 'message-left';

        echo '<div class="message ' . $messageClass . '">';
        echo '<div>' . $row["message"] . '</div>';
        echo '<div class="message-time">' . $row["time"] . '</div>'; 
        echo '</div>';
    }
} else {
    echo "No messages yet.";
}

$stmt->close();
$conn->close();
?>

