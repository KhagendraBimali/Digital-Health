<head>
<link rel="stylesheet" href="../../snr/table.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
        $(document).ready(function() {
            
            $('form').submit(function(event) {
                
                event.preventDefault();

                
                var searchTerm = $('input[name="search"]').val();

                
                $.ajax({
                    type: 'GET',
                    url: 'schedule.php',
                    data: { search: searchTerm },
                    success: function(response) {
                        
                        $('#main-content').html(response);
                    }
                });
            });
        });
        $(document).ready(function() {
    $('#download-btn').click(function() {
        
        window.location.href = '/snr/generate_schedule.php';
    });
});


    </script>
<style>
    
.search-bar {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
}

.search-bar form {
    display: flex;
    align-items: center;
}

.search-bar input[type="text"] {
    padding: 8px;
    margin-right: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.search-bar button {
    padding: 8px 12px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-bar button:hover {
    background-color: #0056b3;
}

</style>
</head>
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
    header("Location:../../snr/index.php");
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentID = $_POST['appointment_id'];
    $doctorID = $_POST['doctor'];

    $insertQuery = "INSERT INTO schedule (appointment_id, doctor_id) VALUES (?, ?)";
    $selectQuery = "SELECT COUNT(*) FROM schedule WHERE appointment_id = ? AND doctor_id = ?";
    
    $insertStmt = $conn->prepare($insertQuery);
    $selectStmt = $conn->prepare($selectQuery);

    if ($insertStmt && $selectStmt) {
        $insertStmt->bind_param('ii', $appointmentID, $doctorID);
        $selectStmt->bind_param('ii', $appointmentID, $doctorID);

        $selectStmt->execute();
        $selectStmt->bind_result($existingCount);
        $selectStmt->fetch();
        
        $selectStmt->close();
        if ($existingCount == 0) {
            $insertStmt->execute();
        }
        

        $insertStmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
    header("Location: dashboard.html");
    exit();
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
$stmt->bind_param("s", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointment[]=$row;
    }
}
if ($result === false) {
    die("Error: " . $conn->error);
}

    echo '<h1>Appointments</h1>';
    echo '<div class="search-bar ">
<form id="search-form">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
        <button id="download-btn">Download as Excel</button>

        </div>';
    echo '<div id="data">';
    echo '<table id="tab">';
    echo '<tr>';
    echo '<th>Appointment ID</th>';
    echo '<th>Patient ID</th>';
    echo '<th>Patient Name</th>';
    echo '<th>Appointment Date</th>';
    echo '<th>Appointment Time</th>';
    echo '<th>Department</th>';
    echo '<th>Doctors</th>';
    echo '</tr>';
    if(isset($_GET['search'])){
        $searchTerm = $_GET['search'];
        $searchResults = searchAppointments($searchTerm, $appointment);
    
        if(!empty($searchResults)){
            foreach($searchResults as $row){
        echo '<tr>';
        echo '<td>' . $row["appointment_id"] . '</td>';
        echo '<td>' . $row["patient_id"] . '</td>';
        echo '<td>' . getPatientName($conn, $row["patient_id"]) . '</td>';
        echo '<td>' . $row["appointmentDate"] . '</td>';
        echo '<td>' . $row["appointmentTime"] . '</td>';
        echo '<td>' . $row["department"] . '</td>';
        echo '<td>';
        if(!is_null($row["doctor_id"])){
            echo 'Dr. '. getDoctorName($conn, $row["appointment_id"]);
        }
        $appointmentDateTime = new DateTime($row['appointmentDate'] . ' ' . $row['appointmentTime']);
      
              if ($appointmentDateTime > new DateTime()) {
              echo '<br>';
              echo '<form action="schedule.php" method="post" style="text-align:center;">';
              echo '<input type="hidden" name="appointment_id" value="' . $row['appointment_id'] . '">';
              echo '<select id="doctor" name="doctor" onchange="this.form.submit()">';
              echo '<option value="">Assign Doctors</option>';
              echo getDoctors($conn, $row["hospital_id"]);
              echo '</select>';
              echo '<br>';
              echo '</form>';
              }
              echo '</td>';
                echo '</tr>';
    }
}}  else if(!is_null($appointment)){
    foreach($appointment as $row){
    echo '<tr>';
    echo '<td>' . $row["appointment_id"] . '</td>';
    echo '<td>' . $row["patient_id"] . '</td>';
    echo '<td>' . getPatientName($conn, $row["patient_id"]) . '</td>';
    echo '<td>' . $row["appointmentDate"] . '</td>';
    echo '<td>' . $row["appointmentTime"] . '</td>';
    echo '<td>' . $row["department"] . '</td>';
    echo '<td>';
    if(!is_null($row["doctor_id"])){
        echo 'Dr. '. getDoctorName($conn, $row["appointment_id"]);
    }
    $appointmentDateTime = new DateTime($row['appointmentDate'] . ' ' . $row['appointmentTime']);
  
          if ($appointmentDateTime > new DateTime()) {
          echo '<br>';
          echo '<form action="schedule.php" method="post" style="text-align:center;">';
          echo '<input type="hidden" name="appointment_id" value="' . $row['appointment_id'] . '">';
          echo '<select id="doctor" name="doctor" onchange="this.form.submit()">';
          echo '<option value="">Assign Doctors</option>';
          echo getDoctors($conn, $row["hospital_id"]);
          echo '</select>';
          echo '<br>';
          echo '</form>';
          }
          echo '</td>';
    echo '</tr>';
}
}
    echo '</table>';
    echo '</div>';

    function searchAppointments($searchTerm, $appointments) {
        $searchResults = [];
        foreach ($appointments as $appointment) {
            if (stripos($appointment['patient_name'], $searchTerm) !== false || 
                stripos($appointment['patient_id'], $searchTerm) !== false) {
                $searchResults[] = $appointment;
            }
        }
        return $searchResults;
    }
    
    function getPatientName($conn, $patientId){
        $patientQuery = "SELECT full_name FROM patients_signup WHERE patient_id = ?";
              $stmt = $conn->prepare($patientQuery);
              $stmt->bind_param("s", $patientId);
              $stmt->execute();
              $res = $stmt->get_result();
              $patient = $res->fetch_assoc();
              $patientName = $patient["full_name"];
              return $patientName;
    }
    function getDoctorName($conn, $appointmentId){
        $Query = "SELECT d.full_name FROM schedule s JOIN doctor_signup d ON s.doctor_id = d.doctor_id WHERE s.appointment_id = ?";
        $stmt = $conn->prepare($Query);
        $stmt->bind_param("s", $appointmentId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()){
            $doctorNames[] = $row["full_name"];
        }
        return implode('<br>', $doctorNames);
        }
        function getDoctors($conn, $hospital_id){
            $doctorsQuery = "SELECT doctor_id, full_name FROM doctor_signup WHERE hospital_id=?";
      $stmt = $conn->prepare($doctorsQuery);
      $stmt->bind_param("s", $hospital_id);
      $stmt->execute();
      $doctorsResult = $stmt->get_result();
      $doctorOptions = '';
      
      if ($doctorsResult) {
          while ($doctor = $doctorsResult->fetch_assoc()) {
              $doctorOptions .= '<option value="' . $doctor['doctor_id'] . '">' . $doctor['full_name'] . '</option>';
          }
        }
        return $doctorOptions;
    }
    $conn->close();
?>