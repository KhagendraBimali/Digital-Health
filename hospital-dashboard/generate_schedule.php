<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hospital';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['hospital_id'])) {
    die("Hospital ID not set in session.");
}

$hospital_id = $_SESSION['hospital_id'];

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="appointments.csv"');

$output = fopen('php://output', 'w');

// Output CSV headers
fputcsv($output, array('Appointment ID', 'Patient ID', 'Patient Name', 'Appointment Date', 'Appointment Time', 'Department', 'Doctor Name'));

// Fetch appointment data
$query = "SELECT a.appointment_id, a.patient_id, p.full_name AS patient_name, a.appointmentDate, a.appointmentTime, a.department, d.full_name AS doctor_name FROM appointment a 
JOIN patients_signup p ON a.patient_id = p.patient_id
LEFT JOIN schedule s ON a.appointment_id = s.appointment_id
LEFT JOIN doctor_signup d ON s.doctor_id = d.doctor_id
WHERE a.hospital_id = ?";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    $stmt->close();
} else {
    // Handle query preparation error
    fputcsv($output, array("Error preparing query: " . $conn->error));
}

fclose($output);
$conn->close();

?> 