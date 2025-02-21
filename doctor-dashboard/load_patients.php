<script>
        $(document).ready(function() {
            
            $('form').submit(function(event) {
                
                event.preventDefault();

                
                var searchTerm = $('input[name="search"]').val();

                
                $.ajax({
                    type: 'GET',
                    url: 'load_patients.php',
                    data: { search: searchTerm },
                    success: function(response) {
                        
                        $('#main-content').html(response);
                    }
                });
            });
        });
    </script>
<div class="search-bar">
        <form>
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
    </div>
<?php
session_start();

$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "hospital";

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$doctor_id = $_SESSION['doctor_id'];

$sql = "SELECT DISTINCT p.patient_id, p.full_name, p.profile 
        FROM patients_signup p 
        JOIN appointment a ON p.patient_id = a.patient_id 
        JOIN schedule s on a.appointment_id = s.appointment_id
        WHERE s.doctor_id = $doctor_id";

$result = $conn->query($sql);
$patients = [];
if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
function searchPatients($searchTerm, $patients) {
    $searchResults = [];
    foreach ($patients as $patient) {
        if (stripos($patient['full_name'], $searchTerm) !== false) {
            $searchResults[] = $patient;
        }
    }
    return $searchResults;
}
if(isset($_GET['search'])){
    $searchTerm = $_GET['search'];
    $searchResults = searchPatients($searchTerm, $patients);
    if(!empty($searchResults)){
        foreach($searchResults as $row){
            echo '<div id="doctor" data-patient-id="' . $row['patient_id'] . '" data-patient-name="' .$row['full_name'].'" data-patient-profile="'.$row['profile'].'"><img id="docimg"src="' . $row["profile"] . '">  <h4>' . $row["full_name"] . '<h4></div>';
        }
    }
}else {
    if(!is_null($patients)){
        foreach($patients as $row){
            echo '<div id="doctor" data-patient-id="' . $row['patient_id'] . '" data-patient-name="' .$row['full_name'].'" data-patient-profile="'.$row['profile'].'"><img id="docimg"src="' . $row["profile"] . '">  <h4>' . $row["full_name"] . '<h4></div>';
        }
    }
}
$conn->close();
?>

<style>
    #doctor {
        border: 1px solid #ccc;
        border-radius: 8px;
        margin: 10px;
        padding: 5px;
        background-color: #fff;
        display:flex;
    }
    h4 {
        margin-top: 30px;
        margin-left: 20px;
    }
    
    #docimg {
        max-width: 100%;
        max-height: 70px;
        border-radius: 60%;
    }
    #doctor:hover{
        background-color: #f9f9f9;
    }
    .search-bar {
        margin-bottom: 20px;
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
    
