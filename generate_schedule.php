<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hospital';


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$hospital_id = $_SESSION['hospital_id'];

$query = "SELECT a.*, s.doctor_id, p.full_name AS patient_name, d.full_name AS doctor_name FROM appointment a 
JOIN patients_signup p ON a.patient_id = p.patient_id
LEFT JOIN schedule s ON a.appointment_id = s.appointment_id
LEFT JOIN doctor_signup d ON s.doctor_id = d.doctor_id
WHERE a.hospital_id = ?
GROUP BY a.appointment_id
ORDER BY 
    CASE 
        WHEN a.appointmentDate > CURDATE() OR (a.appointmentDate = CURDATE() AND a.appointmentTime > CURTIME()) 
        THEN 0 
        ELSE 1 
    END, 
    a.appointmentDate ASC, 
    a.appointmentTime ASC;";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['hospital_id']);
$stmt->execute();
$result = $stmt->get_result();


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


$sheet->setCellValue('A1', 'Appointment ID');
$sheet->setCellValue('B1', 'Patient ID');
$sheet->setCellValue('C1', 'Patient Name');
$sheet->setCellValue('D1', 'Appointment Date');
$sheet->setCellValue('E1', 'Appointment Time');
$sheet->setCellValue('F1', 'Department');
$sheet->setCellValue('G1', 'Doctor Name');


$row = 2; 
while ($row_data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $row_data['appointment_id']);
    $sheet->setCellValue('B' . $row, $row_data['patient_id']);
    $sheet->setCellValue('C' . $row, $row_data['patient_name']);
    $sheet->setCellValue('D' . $row, $row_data['appointmentDate']);
    $sheet->setCellValue('E' . $row, $row_data['appointmentTime']);
    $sheet->setCellValue('F' . $row, $row_data['department']);
    $sheet->setCellValue('G' . $row, $row_data['doctor_id'] ? 'Dr. ' . getDoctorName($conn, $row_data['appointment_id']) : '');
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="appointments.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);


$writer->save('php://output');

$stmt->close();
$conn->close();




function getDoctorName($conn, $appointmentId) {
    $query = "SELECT d.full_name FROM schedule s JOIN doctor_signup d ON s.doctor_id = d.doctor_id WHERE s.appointment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $appointmentId);
    $stmt->execute();
    $res = $stmt->get_result();
    $doctorNames = [];
    while ($row = $res->fetch_assoc()) {
        $doctorNames[] = $row["full_name"];
    }
    return implode(', ', $doctorNames);
}
?>