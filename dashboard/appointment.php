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
if (!isset($_SESSION['patient_id'])) {
    header("Location:../../snr/index.php");
    exit();
  }
$patient_id = $_SESSION['patient_id'];

function getNameByID($id, $conn) {
    $full_name='';
    $query = "SELECT full_name FROM admin_signup WHERE hospital_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($full_name);
    $stmt->fetch();
    return $full_name;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $department = $_POST['department'];
    $hospital = $_POST['hospital'];
    
    $query = "INSERT INTO appointment (patient_id, appointmentDate, appointmentTime, department, hospital_id) VALUES (?,?,?,?,?)";
    $sql = "INSERT INTO schedule (appointment_id) Value(?)";
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $patient_id, $appointmentDate, $appointmentTime, $department, $hospital);

        $stmt->execute();

        
        header("Location: dashboard.html");
        exit;
    } catch (PDOException $e) {
        
        echo "Error: " . $e->getMessage();
    }
}
try{
    $query = "SELECT * FROM appointment WHERE patient_id=? ORDER BY appointmentDate DESC, appointmentTime DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();


if ($result->num_rows > 0) {
    echo '<div id="bookForm" class="form-section active">';
    echo '<h1>Appoinments</h1>';
    echo'<button onclick="showForm(`view`)" style="margin-top:10px;">Book Appointment </button>';
    echo '<div id="data">';
    echo '<table id="tab">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Appointment Date</th>';
    echo '<th>Appointment Time</th>';
    echo '<th>Hospital</th>';
    echo '<th>Department</th>';
    echo '</tr>';

    while ($row = $result->fetch_assoc()) {
        $hospital_id = $row['hospital_id'];
        $full_name = getNameByID($hospital_id, $conn);
        echo '<tr >';
        echo '<td>' . $row["appointment_id"] . '</td>';
        echo '<td>' . $row["appointmentDate"] . '</td>';
        echo '<td>' . $row["appointmentTime"] . '</td>';
        echo '<td>' . $full_name . '</td>';
        echo '<td>' . $row["department"] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo'</div>';
    echo'<button onclick="showForm(`view`)" style="margin-top:10px;">Book Appointment </button>';
    echo'</div>';
} else {
    echo '<div id="bookForm" class="form-section active">';
    echo '<div id="data">';
    echo "No appointments yet.";
    echo'<button onclick="showForm(`view`)" style="margin-top:10px;">Book Appointment </button>';
    echo '</div>';
    echo '</div>';
}
$hospitalsQuery = "SELECT hospital_id, full_name FROM admin_signup";
$hospitalsResult = $conn->query($hospitalsQuery);
$hospitalOptions = '';



if ($hospitalsResult) {
    
    while ($hospital = $hospitalsResult->fetch_assoc()) {
        
        $hospitalOptions .= '<option value="' . $hospital['hospital_id'] . '">' . $hospital['full_name'] . '</option>';
     
        
    }
} else {
    
    echo "Error fetching hospitals: " . $conn->error;
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


function getRecommendedDoctors($location, $medicalHistory, $conn) {
    $recommendedDoctors = [];
    if($medicalHistory === null){
        
            
            $keywords = explode(',', $medicalHistory);
        
            
            $specialityPatterns = array_map(function ($keyword) {
                return '%' . trim($keyword) . '%';
            }, $keywords);
        
            
            $sql = "SELECT * FROM doctor_signup WHERE " . implode(' OR ', array_fill(0, count($keywords), 'speciality LIKE ?'));
            $stmt = $conn->prepare($sql);
        
            
            $stmt->bind_param(str_repeat('s', count($specialityPatterns)), ...$specialityPatterns);
    } else {
        $specialityPatterns = $location;
        $sql = "SELECT * FROM doctor_signup WHERE address LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $specialityPatterns);
    }


    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recommendedDoctors[] = $row;
        }
    }

    return $recommendedDoctors;
}




function getRecommendedHospitals($location, $medicalHistory, $conn) {
    $recommendedHospitals = [];

    $keywords = explode(',', $medicalHistory);

    
    $specialityPatterns = array_map(function ($keyword) {
        return '%' . trim($keyword) . '%';
    }, $keywords);

    
    $sql = "SELECT DISTINCT a.full_name, a.address, a.profile FROM admin_signup a
            JOIN doctor_signup d ON a.hospital_id = d.hospital_id
            WHERE a.address LIKE ? AND (".implode(' OR ', array_fill(0, count($specialityPatterns), 'd.speciality LIKE ?')).")";

    $stmt = $conn->prepare($sql);

    
    $stmt->bind_param("s" . str_repeat('s', count($specialityPatterns)), $location, ...$specialityPatterns);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recommendedHospitals[] = $row;
        }
    }

    return $recommendedHospitals;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup Form</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../../snr/table.css">
    <link rel="stylesheet" type="text/css" href="../../snr/patient_signup.css">
    <style>

        .form-section.active {
            display: block;
        }
    
        .form-section {
            display: none;
        }

    
        form {
            max-width: 400px;
            margin: 0 auto;
        }
    
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
    
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
    
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        #viewForm {
            font-family: Arial, sans-serif;   
        }


</style>
<script>
    function validateForm() {
    var appointmentDate = document.getElementById('appointmentDate').value;
    var appointmentTime = document.getElementById('appointmentTime').value;
    var hospital = document.getElementById('hospital').value;
    var department = document.getElementById('department').value;

    var isValid = true;

    
    var errors = document.getElementsByClassName('error');
    for (var i = 0; i < errors.length; i++) {
        errors[i].innerText = '';
    }

    
    if (appointmentDate === '') {
        document.getElementById('appointmentDateError').innerText = 'Please select an appointment date';
        isValid = false;
    } else {
        var selectedDate = new Date(appointmentDate);
        var currentDate = new Date();
        
        
        if (selectedDate < currentDate) {
            document.getElementById('appointmentDateError').innerText = 'Appointment date should be today or in the future';
            isValid = false;
        }
    }

    
    if (appointmentTime === '') {
        document.getElementById('appointmentTimeError').innerText = 'Please select an appointment time';
        isValid = false;
    }

    
    if (hospital === '') {
        document.getElementById('hospitalError').innerText = 'Please select a hospital';
        isValid = false;
    }

    
    if (department === '') {
        document.getElementById('departmentError').innerText = 'Please select a department';
        isValid = false;
    }

    return isValid;
}
function showForm(formType) {
  var bookForm = document.getElementById('bookForm');
  var viewForm = document.getElementById('viewForm');

  if (formType === 'book') {
      bookForm.style.display = 'block';
      viewForm.style.display = 'none';
  } else if (formType === 'view') {
      bookForm.style.display = 'none';
      viewForm.style.display = 'block';
  }
}
</script>
<style>
    #imageContainer {
  display: flex;
  flex-wrap: nowrap;
  overflow-x: auto;
  margin: 20px;
}

.doctorCard {
  width: 195px;
  height: 270px; 
  margin-right: 10px;
  flex-shrink: 0;
  text-align: center;
  border: 2px solid #ddd; 
  border-radius: 8px; 
  overflow: hidden; 
}

.doctorImage {
  width: 100%;
  height: 70%;
  object-fit: cover;
  border-bottom: 2px solid #ddd; 
}

.doctorName {
  font-weight: bold;
}

.speciality {
  font-style: italic;
}


</style>
</head>
<body>
<div id="viewForm" class="form-section">
<h1>Book Appointment</h1>
    <div class="signup-container"style="margin-top:inherit;">
        <div class="right-section">
            <div class="signup-box">
                <form action="appointment.php" method="post">
                    <div id="appointmentForm">
                        <label for="appointmentDate">Appointment Date:</label>
                        <input type="date" id="appointmentDate" name="appointmentDate">
                        <span id="appointmentDateError" class="error"></span><br>
                        <label for="appointmentTime">Appointment Time:</label>
                        <input type="time" id="appointmentTime" name="appointmentTime">
                        <span id="appointmentTimeError" class="error"></span><br>
                        <label for="hospital">Hospital:</label>
                        <select id="hospital" name="hospital">
                            <option value="">Select Hospital</option>
                            <?php echo $hospitalOptions; ?>
                        </select><br>
                        
                        <span id="hospitalError" class="error"></span><br>
   
                        <label for="department">Department:</label>
                        <select id="department" name="department">
                            <?php 
                            echo '<option value="">Select Department</option>';
                            echo $doctorOptions; 
                            ?>
                        </select><br>
                        <span id="departmentError" class="error"></span><br>
                    </div>
                    <div style="display:flex;">
                        <button type="button" onclick="showForm('book')">Back</button>
                        <button type="submit" onclick="return validateForm()">Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    $sqlUser = "SELECT medical_history, address FROM patients_signup WHERE patient_id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $patient_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $userData = $resultUser->fetch_assoc();
    $medicalHistory = $userData['medical_history'];
    $userAddress = $userData['address'];

    
    $recommendedDoctors = getRecommendedDoctors($userAddress, $medicalHistory, $conn);
    $recommendedHospitals = getRecommendedHospitals($userAddress, $medicalHistory, $conn);

    

    echo '<h2>Recommended Doctors:</h2>';
    echo '<div id="imageContainer">';
    foreach ($recommendedDoctors as $doctor) {
        echo '<div class="doctorCard">';
        echo '<img class="doctorImage" src="' . $doctor['profile'] . '" alt="Dr.' . $doctor['full_name'] . '">';
        echo '<div class="doctorName">Dr. ' . $doctor['full_name'] . '</div>';
        echo '<div class="doctorName">' . $doctor['qualification'] . '</div>';
        echo '<div class="speciality">' . explode(" ", $doctor['speciality'])[0] . '</div>';
        echo '<div class="speciality">' . getNameByID($doctor['hospital_id'], $conn) . '</div>';
        echo '</div>';
    }
    echo '</div>';
    

    echo '<h2>Recommended Hospitals:</h2>';
    echo '<div id="imageContainer">';
    foreach ($recommendedHospitals as $hospital) {
        echo '<div class="doctorCard">';
        echo '<img class="doctorImage" src="' . $hospital['profile'] . '" alt="Dr.' . $hospital['full_name'] . '">';
        echo '<div class="doctorName">' . $hospital['full_name'] . '</div>';
        echo '<div class="speciality">' .$hospital['address'] . '</div>';
        echo '</div>';
    }
    echo '</div>';
}
?>
    
</div>
        
    <script>
        
        function updateDoctorOptions() {
            var hospitalId = document.getElementById('hospital').value;
            var doctorSelect = document.getElementById('department');
            doctorSelect.innerHTML = ''; 

            
            fetch('speciality.php?hospital_id=' + hospitalId)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                data.forEach(doctor => {
                    var option = document.createElement('option');
                    option.value = doctor;
                    option.text = doctor;
                    doctorSelect.add(option);
                });
            }
                })
                .catch(error => console.error('Error:', error));
        }

        
        document.getElementById('hospital').addEventListener('change', updateDoctorOptions);

        
        updateDoctorOptions();
    </script>
</body>
</html>