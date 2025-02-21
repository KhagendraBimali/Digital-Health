<?php



$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "hospital";

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["hospital_id"])) {
    $hospitalId = $_GET["hospital_id"];

    
    $doctorQuery = "SELECT speciality FROM doctor_signup WHERE hospital_id = ?";
    
    $stmtDoctor = $conn->prepare($doctorQuery);
    $stmtDoctor->bind_param("s", $hospitalId);
    $stmtDoctor->execute();
    $doctorResult = $stmtDoctor->get_result();

    $doctorSpecialities = array();

    
    $doctorSpecialities[] = "Select Department";

    while ($doctor = $doctorResult->fetch_assoc()) {
        
        if (!in_array($doctor['speciality'], $doctorSpecialities)) {
            $doctorSpecialities[] = explode(" ", $doctor['speciality'])[0];
        }
    }

    
    header('Content-Type: application/json');
    echo json_encode($doctorSpecialities);
} else {
    
    http_response_code(400);
    echo "Invalid request";
}
?>
