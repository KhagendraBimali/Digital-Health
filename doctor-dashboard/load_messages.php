<?php
include('db_connection.php');

try {
    $patientId = $_GET['patient_id'];
    $doctorId = $_SESSION['doctor_id'];
    $sql = "SELECT * FROM messages WHERE doctor_id='$doctorId' AND patient_id = '$patientId' ORDER BY time";
    $stmt = $conn->query($sql);

    if ($stmt) {
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messageClass = ($row["type"] === 'patient') ? 'message-left' : 'message-right';
                if ($row["type"] === 'doctor') {
                    $messageClass = 'message-right';
                }

                echo '<div class="message ' . $messageClass . '">';
                echo '<div>' . $row["message"] . '</div>';
                echo '<div class="message-time">' . $row["time"] . '</div>';
                echo '</div>';
            }
        } else {
            echo "No messages yet.";
        }
    } else {
        throw new Exception("Query execution failed.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
