<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information</title>
</head>

<style>
     h1 {
        color: #333; 
        text-align: center;
        margin-top: 20px; 
    }
        
 form {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #info {
            display:flex;
        }

        label {
            display: flex;
            font-weight: bold;
            align-items: center;
        }

        input[type="text"] {
            margin-left: 15px;
            width: 100%;
            padding: 5px;
            border: 1px solid #d90909; 
            border-radius: 4px;
        }

        input[type="file"] {
            display:none;
        }

        #img {
            max-width: 800px;
            height: auto;
            display: block;
            margin-bottom: 15px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }
        
        input[type="submit"], button {
            display: block; 
            margin: 0 auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            
        }
        
        input[type="submit"]:hover, button:hover {
            background-color: #0056b3;
        }
        #profile {
            text-align: -webkit-center;
            margin-left: auto;
        }
        #profile img {
            max-width: 200px;
            height: auto;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        #imageContainer {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            margin: 20px;
        }

        span {
            margin-left: 15px;
        }
        h4{
            margin: 0px;
            margin-left: 15px;
        }
        #information {
            width: 100%;
            max-inline-size: fit-content;
        }
        #fileInputsContainer {
            margin-left:-15px;
        }
        #fileInputsContainer .file-upload {
    border: 2px solid red;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    color: #0066cc;
    text-align: center;
  }

</style>
<body>
<h1>Personal Information</h1>
<div style="display:none;">
        <img id="back" src="../pic/back.png" alt="back">
    </div>
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

$hospitalId = $_SESSION['hospital_id'];
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $profile =$_FILES['profile'];
    
    
    if ($profile['error'] == UPLOAD_ERR_OK) {
        $profileDirectory = "C:/xampp/htdocs/snr/pic/";
        $targetProfile = "/snr/pic/" . basename($profile['name']);
        $profilePath = $profileDirectory . basename($profile['name']);
        
        move_uploaded_file($profile['tmp_name'], $profilePath);
        
        
    }
    $query = 'UPDATE admin_signup SET full_name=?, address=? profile=? WHERE hospital_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $full_name, $address, $targetProfile, $hospitalId) ;
    $stmt->execute() ;
    $stmt->close();
    header('Location: dashboard.html');
    exit;
}

$query = "SELECT * FROM admin_signup WHERE hospital_id =?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hospitalId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<form action="profile.php" method="POST" enctype="multipart/form-data">';
        echo '<div id=info>';
        echo '<div id ="information">';
        echo '<label><strong>Name: </strong> <span id="nameDisplay">' . $row['full_name'] . '</span><input type="text" name="full_name" value="' . $row['full_name'] . '" style="display:none;"></label><br>';
        echo '<label><strong>Address: </strong> <span id="addressDisplay">' . $row['address'] . '</span><input type="text" name="address" value="' . $row['address'] . '" style="display:none;"></label><br>';
        echo '<label><strong>Email: </strong><h4>' . $row['email'] . '</h4></label><br>';
        echo '</div>';               
        echo '<div id="profile">';
        echo '<img id="img" src="' . $row['profile'] . '"><br>';
        echo '<div id="fileInputsContainer">';
        echo '<input type="file" id="profilepic" name="profile" style="display:none" accept="image/*">';
        echo '<span class="file-upload" id="uploadLabel" style="display:none"><strong>Upload Profile Picture</strong></span>';        echo '</div><br>';
        echo '</div>';               
        echo '</div>';
        echo '</div>';
        
    }
    echo '<button id =submitButton type="button" onclick="toggleEditMode()">Edit Information</button>';
    echo '<input type="submit" name="submit" id="submitButton" style="display: none">';
    echo '</form>';
} else {
    echo 'No personal information found.';
}
$stmt->close();
?>

<script>
    function toggleEditMode() {
    
    var backImage = document.getElementById('back');

    
    backImage.style.display = (backImage.style.display === 'none') ? 'block' : 'none';

    
    var inputElements = document.querySelectorAll('form input');
    var spanElements = document.querySelectorAll('form span');

    inputElements.forEach(function (element) {
    if (element.type !== 'submit') {
        element.style.display = (element.style.display === 'none') ? 'inline' : 'none';
    } else {
        element.style.display = (element.style.display === 'none') ? 'block' : 'none';
    }
});

    spanElements.forEach(function (element) {
        element.style.display = (element.style.display === 'none') ? 'inline' : 'none';
    });

    
    var submitButton = document.getElementById('submitButton');

    submitButton.style.display = (submitButton.style.display === 'none') ? 'block' : 'none';
}
function fileUpload() {
    var fileLabel = document.getElementById('uploadLabel');
    var fileInput = document.getElementById('profilepic');


    fileLabel.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function (event) {
        
        console.log('File selected:', event.target.files[0]);
    });

}
fileUpload();

</script>

</body>

</html>