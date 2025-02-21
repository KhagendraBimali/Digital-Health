<head>
    <link rel="stylesheet" href="../../snr/table.css">
</head>
<h1>Your Appointments</h1>
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

$doctorID = $_SESSION['doctor_id'];
$query = "SELECT a.patient_id, ps.full_name, a.appointment_id, a.appointmentDate, a.appointmentTime FROM appointment a JOIN schedule s ON a.appointment_id = s.appointment_id JOIN patients_signup ps ON a.patient_id = ps.patient_id WHERE doctor_id=?;";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $doctorID);
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
function compareAppointments($a, $b) {
    $dateComparison = strcmp($a['appointmentDate'], $b['appointmentDate']);
    
    if ($dateComparison == 0) {
        return strcmp($a['appointmentTime'], $b['appointmentTime']);
    }

    return $dateComparison;
}


usort($rows, 'compareAppointments');

if (count($rows) > 0) {
    echo '<div id="data">';
    echo '<table id="tab">';
    echo '<tr>';
    echo '<th>Patient ID</th>';
    echo '<th>Patient Name</th>';
    echo '<th>Appointment ID</th>';
    echo '<th>Appointment Date</th>';
    echo '<th>Appointment Time</th>';
    echo '</tr>';
    
    
    foreach ($rows as $row) {
        
        if (strtotime($row['appointmentDate']) >= strtotime(date('Y-m-d'))) {
            echo '<tr>';
            echo '<td>' . $row['patient_id'] . '</td>';
            echo '<td>' . $row['full_name'] . '</td>';
            echo '<td>' . $row['appointment_id'] . '</td>';
            echo '<td>' . $row['appointmentDate'] . '</td>';
            echo '<td>' . $row['appointmentTime'] . '</td>';
            echo '</tr>';
        }
    }

    echo '</table>';
    echo '</div>';
}
?>
