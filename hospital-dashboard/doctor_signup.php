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
if (!isset($_SESSION['hospital_id'])) {
    header("Location:../../snr/index.php");
    exit();
  }
  
$hospital_id = $_SESSION['hospital_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $qualification = $_POST["qualification"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $speciality = $_POST["speciality"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

if ($password !== $confirm_password) {
    echo "Error: Passwords do not match.";
    exit;
}

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO doctor_signup (full_name, qualification, address, email, phone, password, speciality, hospital_id) VALUES (?,?,?,?,?,?,?,?)";
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $full_name, $qualification, $address, $email, $phone, $hashed_password, $speciality, $hospital_id);
        $stmt->execute();

        header("Location: dashboard.html");
        exit;

}
try{
    $query = "SELECT distinct * FROM doctor_signup WHERE hospital_id=$hospital_id";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
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
    echo '<div id="bookForm" class="form-section active">';
    echo '<h1 style="background-color:#fff;">Doctors</h1>';
    echo '<div id ="search-bar" class="search-bar"style="display:flex; justify-content:space-between; margin-bottom: 10px;">
    <form id="search-form">
    <input type="text" name="search" placeholder="Search...">
    <button type="submit">Search</button>
    </form>
    <button onclick="showForm(`view`)">Register Doctor</button>
    </div>';
    echo '<div id="data">';
    echo '<table id="tab">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Name</th>';
    echo '<th>Speciality</th>';
    echo '<th>Address</th>';
    echo '<th>Email</th>';
    echo '<th>Phone</th>';
    echo '</tr>';

    if(isset($_GET['search'])){
        $searchTerm = $_GET['search'];
        $searchResults = searchDoctors($searchTerm, $doctors);
    
        if(!empty($searchResults)){
            foreach($searchResults as $row){
        echo '<tr >';
        echo '<td>' . $row["doctor_id"] . '</td>';
        echo '<td>Dr. ' . $row["full_name"] .'<br>'. $row["qualification"] . '</td>';
        echo '<td>' . explode(" ",$row["speciality"])[0] . '</td>';
        echo '<td>' . $row["address"] . '</td>';
        echo '<td>' . $row["email"] . '</td>';
        echo '<td>' . $row["phone"] . '</td>';
        echo '</tr>';
    }
        }
    }else if(!is_null($doctors)){
        foreach($doctors as $row){
            echo '<tr >';
            echo '<td>' . $row["doctor_id"] . '</td>';
            echo '<td>Dr. ' . $row["full_name"] .'<br>'. $row["qualification"] . '</td>';
            echo '<td>' . explode(" ",$row["speciality"])[0] . '</td>';
            echo '<td>' . $row["address"] . '</td>';
            echo '<td>' . $row["email"] . '</td>';
            echo '<td>' . $row["phone"] . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo'</div>';
    }   
    else {
        echo '<div id="bookForm" class="form-section active">';
        echo '<div id="data">';
        echo "No patient information found";
        echo'<button onclick="showForm(`view`)" style="margin-top:10px;">Register Doctor </button>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup Form</title>
    <link rel="stylesheet" href="../../snr/table.css">
    <link rel="stylesheet" type="text/css" href="../../snr/patient_signup.css">
    <script src="https:

<script>
        $(document).ready(function() {
            
            $('#search-form').submit(function(event) {
                
                event.preventDefault();

                
                var searchTerm = $('input[name="search"]').val();

                
                $.ajax({
                    type: 'GET',
                    url: 'doctor_signup.php',
                    data: { search: searchTerm },
                    success: function(response) {
                        
                        $('#main-content').html(response);
                    }
                });
            });
        });
    </script>
    <script>
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

        function validateForm() {
            var fullName = document.getElementById('full_name').value.trim();
            var speciality = document.getElementById('speciality').value.trim();
            var address = document.getElementById('address').value.trim();
            var email = document.getElementById('email').value.trim();
            var phoneNumber = document.getElementById('phone').value.trim();
            var password = document.getElementById('password').value.trim();
            var confirmPassword = document.getElementById('confirm_password').value.trim();
            var qualification = document.getElementById('qualification').value.trim();


            document.getElementById('fullNameError').innerText = '';
            document.getElementById('specialityError').innerText = '';
            document.getElementById('addressError').innerText = '';
            document.getElementById('emailError').innerText = '';
            document.getElementById('phoneError').innerText = '';
            document.getElementById('passwordError').innerText = '';
            document.getElementById('confirmPasswordError').innerText = '';
            document.getElementById('qualificationError').innerText = '';

            var isValid = true;

            if (fullName === '') {
                document.getElementById('fullNameError').innerText = 'Full Name cannot be blank.';
                isValid = false;
            }

            if (qualification === '') {
                document.getElementById('qualificationError').innerText = 'Qualification cannot be blank.';
                isValid = false;
            }

            if (speciality === '') {
                document.getElementById('specialityError').innerText = 'Speciality cannot be blank.';
                isValid = false;
            }

            if (address === '') {
                document.getElementById('addressError').innerText = 'Address cannot be blank.';
                isValid = false;
            }

            if (email === '') {
                document.getElementById('emailError').innerText = 'Email cannot be blank.';
                isValid = false;
            }

            if (phoneNumber === '') {
                document.getElementById('phoneError').innerText = 'Phone number is required.';
                isValid = false;
            } else if (phoneNumber.length !== 10 || isNaN(phoneNumber)) {
                document.getElementById('phoneError').innerText = 'Phone number should be 10 digits.';
                isValid = false;
            }

            if (password === '') {
                document.getElementById('passwordError').innerText = 'Password cannot be blank.';
                isValid = false;
            }

            if (confirmPassword === '') {
                document.getElementById('confirmPasswordError').innerText = 'Please confirm password.';
                isValid = false;
            }

            if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').innerText = 'Passwords do not match.';
                isValid = false;
            }

            return isValid;
        }
    </script>
    <style>
        h1 {
            color: #333; 
            text-align: center;
            margin-top: 20px; 
        }
        form {
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }
        .form-section.active {
            display: block;
        }
    
        .form-section {
            display: none;
        }
        #viewForm {
            text-align: center;
            font-family: Arial, sans-serif;   
        }
        .error{
            color:red;
        }
        <style>
.search-bar {
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;

}

.search-bar form {
    box-shadow:none;
    margin: 0px;
    display: flex;
    align-items: center;
    padding: 0px;
}

.search-bar input[type="text"] {
    padding: 8px;
    margin-right: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 0px;
}

.search-bar button {
    padding: 8px 12px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 0px;
}

.search-bar button:hover {
    background-color: #0056b3;
}

</style>
        
</head><body>
    <div id="viewForm" class="form-section">
        <div class="signup-container" style="margin-top: inherit;">
            <div class="right-section">
                <div class="signup-box">
                    <form id="doctorForm" action="doctor_signup.php" method="post" onsubmit="return validateForm()">
                        <h1>Register Doctor</h1>
                        <div class="input-container">
                            <input type="text" name="full_name" id="full_name" placeholder="Full Name">
                            <span id="fullNameError" class="error"></span>
                            
                            <input type="text" name="qualification" id="qualification" placeholder="Qualification">
                            <span id="qualificationError" class="error"></span>

                            <input type="text" name="speciality" id="speciality" placeholder="Speciality">
                            <span id="specialityError" class="error"></span>

                            <input type="text" name="address" id="address" placeholder="Address">
                            <span id="addressError" class="error"></span>

                            <input type="text" name="email" id="email" placeholder="Email">
                            <span id="emailError" class="error"></span>

                            <input type="text" name="phone" id="phone" placeholder="Phone Number">
                            <span id="phoneError" class="error"></span>

                            <input type="password" name="password" id="password" placeholder="Password">
                            <span id="passwordError" class="error"></span>

                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                            <span id="confirmPasswordError" class="error"></span>
                        </div>
                        <div style="display:flex;">
                            <button type="button" onclick="showForm('book')">Back</button>
                            <button type="submit">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>