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
                    url: 'patient_info.php',
                    data: { search: searchTerm },
                    success: function(response) {
                        
                        $('#main-content').html(response);
                    }
                });
            });
        });
        $(document).ready(function() {
    $('#download-btn').click(function() {
        
        window.location.href = '/snr/generate_patientinfo.php';
        
    });
});
    </script>
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

$hospital_id = $_SESSION['hospital_id'];

$query = "SELECT distinct p.*
          FROM patients_signup p 
          JOIN appointment a ON p.patient_id = a.patient_id 
          WHERE a.hospital_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();
$patients = [];
while ($row = $result->fetch_assoc()){
    $patients[] = $row;
}

if ($result === false) {
    die("Error: " . $conn->error);
}


$displayedPatients = array();
echo '<h1>Patients Information</h1>';
echo '<div class="search-bar">
<form id="search-form">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
        <button id="download-btn">Download as Excel</button>
        </div>';
echo '<div id="data">';
echo '<table id="tab">';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>Name</th>';
echo '<th>Age</th>';
echo '<th>Gender</th>';
echo '<th>Address</th>';
echo '<th>Email</th>';
echo '<th>Phone</th>';
echo '<th>Medical History</th>';
echo '</tr>';
if(isset($_GET['search'])){
    $searchTerm = $_GET['search'];
    $searchResults = searchPatients($searchTerm, $patients);
    if(!empty($searchResults)){
        foreach($searchResults as $row){
            echo '<tr class="patient-row">';
            echo '<td>' . $row["patient_id"] . '</td>';
            echo '<td>' . $row["full_name"] . '</td>';
            echo '<td>' . $row["age"] . '</td>';
            echo '<td>' . $row["gender"] . '</td>';
            echo '<td>' . $row["address"] . '</td>';
            echo '<td>' . $row["email"] . '</td>';
            echo '<td>' . $row["phone"] . '</td>';
            echo '<td>' . $row["medical_history"] . '</td>';
            echo '</tr>';
        }    
    } else{
        echo '</table> No patients found.';
    }
} else{
    foreach($patients as $row){
        echo '<tr class="patient-row">';
        echo '<td>' . $row["patient_id"] . '</td>';
        echo '<td>' . $row["full_name"] . '</td>';
        echo '<td>' . $row["age"] . '</td>';
        echo '<td>' . $row["gender"] . '</td>';
        echo '<td>' . $row["address"] . '</td>';
        echo '<td>' . $row["email"] . '</td>';
        echo '<td>' . $row["phone"] . '</td>';
        echo '<td>' . $row["medical_history"] . '</td>';
        echo '</tr>';
    }
}

echo '</table>';
echo '</div>';

function searchPatients($searchTerm, $patients) {
    $searchResults = [];
    foreach ($patients as $patient) {
        if (stripos($patient['full_name'], $searchTerm) !== false) {
            $searchResults[] = $patient;
        }
        else if(stripos($patient['patient_id'], $searchTerm)!== false){
            $searchResults[] = $patient;
        }
    }
    return $searchResults;
}


$conn->close();
?>

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