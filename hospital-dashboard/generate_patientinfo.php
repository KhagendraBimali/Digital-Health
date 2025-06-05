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
header('Content-Disposition: attachment; filename="patients.csv"');

$output = fopen('php://output', 'w');

// Output CSV headers
fputcsv($output, array('ID', 'Name', 'Age', 'Gender', 'Address', 'Email', 'Phone', 'Medical History'));

// Fetch patient data
$query = "SELECT p.patient_id, p.full_name, p.age, p.gender, p.address, p.email, p.phone, p.medical_history
          FROM patients_signup p 
          JOIN appointment a ON p.patient_id = a.patient_id 
          WHERE a.hospital_id=?";

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