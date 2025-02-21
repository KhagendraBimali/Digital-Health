<script>
        $(document).ready(function() {
            
            $('form').submit(function(event) {
                
                event.preventDefault();

                
                var searchTerm = $('input[name="search"]').val();

                
                $.ajax({
                    type: 'GET',
                    url: 'load_doctors.php',
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
$patient_id=$_SESSION['patient_id'];

$sql = "SELECT DISTINCT ds.doctor_id, ds.profile, ds.full_name
FROM doctor_signup ds
JOIN schedule sc ON ds.doctor_id = sc.doctor_id
JOIN appointment ap ON sc.appointment_id = ap.appointment_id
WHERE ap.patient_id = $patient_id;";

$result = $conn->query($sql);
$doctors= [];
if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
function searchDoctors($searchTerm, $doctors) {
    $searchResults = [];
    foreach ($doctors as $doctor) {
        if (stripos($doctor['full_name'], $searchTerm) !== false) {
            $searchResults[] = $doctor;
        }
    }
    return $searchResults;
}
if(isset($_GET['search'])){
    $searchTerm = $_GET['search'];
    $searchResults = searchDoctors($searchTerm, $doctors);

    if(!empty($searchResults)){
        foreach($searchResults as $row){
            echo '<div id="doctor" data-doctor-id="' . $row['doctor_id'] . '" data-doctor-name="' .$row['full_name'].'" data-doctor-profile="'.$row['profile'].'"><img id="docimg"src="' . $row["profile"] . '">  <h4> Dr. ' . $row["full_name"] . '<h4></div>';
        }
    }
}else {
    if($doctors != null)
        foreach($doctors as $row){
            echo '<div id="doctor" data-doctor-id="' . $row['doctor_id'] . '" data-doctor-name="' .$row['full_name'].'" data-doctor-profile="'.$row['profile'].'"><img id="docimg"src="' . $row["profile"] . '">  <h4> Dr. ' . $row["full_name"] . '<h4></div>';
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
