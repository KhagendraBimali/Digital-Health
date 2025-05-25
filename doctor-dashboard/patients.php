<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients List</title>
    <link rel="stylesheet" href="../../table.css">
    <script src="https:
    <script>
        $(document).ready(function() {
            
            $('form').submit(function(event) {
                
                event.preventDefault();

                
                var searchTerm = $('input[name="search"]').val();

                
                $.ajax({
                    type: 'GET',
                    url: 'patients.php',
                    data: { search: searchTerm },
                    success: function(response) {
                        
                        $('#main-content').html(response);
                    }
                });
            });
        });
    </script>
</head>
<body>
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

    
    if (!isset($_SESSION['doctor_id'])) {
        die("Doctor ID not set in the session");
    }

    $doctor_id = $_SESSION['doctor_id'];

    
    $query = "SELECT DISTINCT p.patient_id, p.full_name FROM patients_signup p
              JOIN appointment a ON p.patient_id = a.patient_id
              JOIN schedule s ON a.appointment_id = s.appointment_id
              WHERE s.doctor_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patients = [];
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $patients[] = $row;
        }
    }

    function searchPatients($searchTerm, $patients) {
        $searchResults = [];
        if(!is_null($patients)){
            foreach ($patients as $patient) {
                if (stripos($patient['full_name'], $searchTerm) !== false) {
                    $searchResults[] = $patient;
                }
            }
        }
        return $searchResults;
    }
    ?>
     <h1>Patients List</h1>
    <div class="search-bar">
        <form>
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <div id="data">
        <table id="tab">
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
            </tr>

            <?php
            if(isset($_GET['search'])){
                $searchTerm = $_GET['search'];
                $searchResults = searchPatients($searchTerm, $patients);
                if(!empty($searchResults)){
                    foreach($searchResults as $row){
                        echo '<tr class="patient-row" >';
                        echo '<td>' . $row["patient_id"] . '</td>';
                        echo '<td>' . $row["full_name"] . '</td>';
                        echo '</tr>';
                    }    
                } else{
                    echo '</table> No patients found.';
                }
            } else{
                if(!is_null($patients)){
                    foreach($patients as $row){
                        echo '<tr class="patient-row" >';
                        echo '<td>' . $row["patient_id"] . '</td>';
                        echo '<td>' . $row["full_name"] . '</td>';
                        echo '</tr>';
                    }
                }
            }
            ?>
        </table>
    </div>

    <?php
    
    $conn->close();
    ?>
<style>
    
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

</body>
</html>
