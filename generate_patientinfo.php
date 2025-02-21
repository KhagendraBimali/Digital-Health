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

$query = "SELECT distinct p.*
          FROM patients_signup p 
          JOIN appointment a ON p.patient_id = a.patient_id 
          WHERE a.hospital_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();




$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


$sheet->setCellValue('A1', 'Patient ID');
$sheet->setCellValue('B1', 'Patient Name');
$sheet->setCellValue('C1', 'Age');
$sheet->setCellValue('D1', 'Gender');
$sheet->setCellValue('E1', 'Address');
$sheet->setCellValue('F1', 'Email');
$sheet->setCellValue('G1', 'Phone');
$sheet->setCellValue('H1', 'Medical History');



$row = 2; 
while ($row_data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $row_data['patient_id']);
    $sheet->setCellValue('B' . $row, $row_data['full_name']);
    $sheet->setCellValue('C' . $row, $row_data['age']);
    $sheet->setCellValue('D' . $row, $row_data['gender']);
    $sheet->setCellValue('E' . $row, $row_data['address']);
    $sheet->setCellValue('F' . $row, $row_data['email']);
    $sheet->setCellValue('G' . $row, $row_data['phone'] );
    $sheet->setCellValue('H' . $row, $row_data['medical_history'] );
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="patient.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);


$writer->save('php://output');


$stmt->close();
$conn->close();

?>