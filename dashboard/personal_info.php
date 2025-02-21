<?php session_start(); ?>
<?php


$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hospital';


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$patientId = $_SESSION['patient_id'];
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $medical_history = $_POST['medical_history'];
    $images = $_FILES['images'];
    $profile =$_FILES['profile'];
    
    $query = 'UPDATE patients_signup SET full_name=?, age=?, address=?, medical_history=? WHERE patient_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $full_name, $age, $address, $medical_history, $patientId) ;
    $stmt->execute() ;
    
    if ($profile['error'] == UPLOAD_ERR_OK) {
        $profileDirectory = "C:/xampp/htdocs/snr/pic/";
        $targetProfile = "/snr/pic/" . basename($profile['name']);
        $profilePath = $profileDirectory . basename($profile['name']);
        
        move_uploaded_file($profile['tmp_name'], $profilePath);
        
        
        $updateProfileQuery = 'UPDATE patients_signup SET profile=? WHERE patient_id=?';
        $updateProfileStmt = $conn->prepare($updateProfileQuery);
        $updateProfileStmt->bind_param('ss', $targetProfile, $patientId);
        $updateProfileStmt->execute();
        $updateProfileStmt->close();
    }
    
    if (is_array($images) && count($images['name']) > 0) {
        for ($i = 0; $i < count($images['name']); $i++) {
            $tDirectory = "C:/xampp/htdocs/snr/pic/";
            
            if ($images['error'][$i] == UPLOAD_ERR_OK) {
                
                $targetDirectory = "/snr/pic/";
                $targetDirectory = $targetDirectory . basename($images['name'][ $i ]);
                $imagePath = $tDirectory . basename($images['name'][$i]);
                
                move_uploaded_file($images['tmp_name'][$i], $imagePath);
                
                $query = 'INSERT INTO medical_report (patient_id, image) VALUES (?, ?)';
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $patientId, $targetDirectory);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    header('Location: dashboard.html');
    exit;
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information</title>
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
    
            .previewImage {
                width: 100%;
                height: 80%;
                object-fit: cover;
                margin-right: 10px;
                flex-shrink: 0;
                cursor: pointer;
            }
    
            #previewModal {
                display: none;
                margin-top: 60px;
                position: fixed;
                z-index: 1;
                padding-top: 50px;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgb(0, 0, 0);
                background-color: rgba(0, 0, 0, 0.9);
            }
    
            #modalContent {
                margin: auto;
                display: block;
                max-width: 80%;
                max-height: 80%;
            }
    
            .close {
                position: absolute;
                top: 15px;
                right: 15px;
                color: white;
                font-size: 30px;
                font-weight: bold;
                cursor: pointer;
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
      #back{
                margin: 16px;
                align-self: center;
                max-width: 100%;
                max-height: 20px;
                border-radius: 30%;
              }
              #back:hover{
                  background-color: #f9f9f9;
              }
              #printButton {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}
    
    </style>
</head>
<body>
<div style="display:flex; justify-content: space-between">
        <img id="back" style="visibility:hidden;" src="../pic/back.png" alt="back">
        <h1>Personal Information</h1>
        <div></div>
    </div>
    <?php
$sql = "SELECT * FROM medical_report WHERE patient_id =?";
$sqlstmt = $conn->prepare($sql);
$sqlstmt->bind_param("s", $patientId);
$sqlstmt->execute();
$sqlResullt = $sqlstmt->get_result();
$query = "SELECT * FROM patients_signup WHERE patient_id =?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $patientId);
$stmt->execute();
$result = $stmt->get_result();
$imagesArray = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<form action="personal_info.php" method="POST" enctype="multipart/form-data">';
        echo '<div id= info>';
        echo '<div id ="information">';
        echo '<label><strong>Name: </strong> <span id="nameDisplay">' . $row['full_name'] . '</span><input type="text" name="full_name" value="' . $row['full_name'] . '" style="display:none;"></label><br>';
        echo '<label><strong>Age: </strong> <span id="ageDisplay">' . $row['age'] . '</span><input type="text" name="age" value="' . $row['age'] . '" style="display:none;"></label><br>';
        echo '<label><strong>Gender: </strong><h4>' . $row['gender'] . '</h4></label><br>';
        echo '<label><strong>Address: </strong> <span id="addressDisplay">' . $row['address'] . '</span><input type="text" name="address" value="' . $row['address'] . '" style="display:none;"></label><br>';
        echo '<label><strong>Email: </strong><h4>' . $row['email'] . '</h4></label><br>';
        echo '<label><strong>Medical History: </strong><span id="historyDisplay">' . $row['medical_history'] . '</span><input type="text"  name="medical_history" value="' . $row['medical_history'] . '" style=" display:none;"></label>';
        echo '</div>';
        echo '<div id="profile">';
        echo '<img id="img" src="' . $row['profile'] . '"><br>';
        echo '<div id="fileInputsContainer">';
        echo '<input type="file" id="profilepic" name="profile" style="display:none" accept="image/*">';
        echo '<span class="file-upload" id="uploadLabel" style="display:none"><strong>Upload Profile Picture</strong></span>';
        echo '</div>';               
        echo '</div>';
        echo '</div>';
        
    }
    
    echo '<label><strong>Medical Records:</strong></label><br>';
    if ($sqlResullt->num_rows > 0) {
        while ($row = $sqlResullt->fetch_assoc()) {
            $imagesArray[] = $row['image'];
        }
    
        $imagesArray = array_reverse($imagesArray);
    
        echo '<div id="imageContainer">';
        foreach ($imagesArray as $index => $image) {
            echo '<div style="width: 138px; height: 177px; margin-right: 10px; flex-shrink: 0;">';
            echo '<img class="previewImage" src="' . $image . '" alt="Medical record image" onclick="openPreview(' . $index . ')">';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '    <div id="previewModal">
        <span class="close" onclick="closePreview()">&times;</span>
        <img id="modalContent" src="" alt="Preview">
        <button id="printButton" onclick="printImage()">Print</button>
    </div>';
    } else {
        echo '<h4>No records found.</h4>';
    }
    echo '<div id="fileInputsContainer">';
    echo '<input type="file" id="mdeical" name="images[]" accept="image/*" multiple ><br>';
    echo '<span class="file-upload" id="uploadMedical" style="display:none"><strong>Upload Medical Records</strong></span>';
    echo '</div><br>';
    echo '<button id =submitButton type="button" onclick="toggleEditMode()">Edit Information</button>';
    echo '<input type="submit" name="submit" id="submitButton" style="display: none">';
    echo '</form>';
} else {
    echo 'No personal information found.';
}
$sqlstmt->close();
$stmt->close();
?>

<script>
            $(document).ready(function () {
            $('#back').on('click', function () {
                
                $.ajax({
                    url: 'personal_info.php', 
                    type: 'GET',
                    success: function (data) {
                        $('#main-content').html(data); 
                    },
                    error: function () {
                        $('#main-content').html('Failed to load personal info.'); 
                    }
                });
            });
        });
    function toggleEditMode() {
    
    var backImage = document.getElementById('back');

    
    backImage.style.visibility = (backImage.style.visibility === 'hidden') ? 'visible' : 'hidden';

    
    var inputElements = document.querySelectorAll('form input');
    var spanElements = document.querySelectorAll('form span');

    inputElements.forEach(function (element) {
    if (element.type === 'file') {
        element.style.display = 'none';
    } else if(element.type !== 'submit'){
        element.style.display = (element.style.display === 'none') ? 'inline' : 'none';        
    } 
    else {
        element.style.display = (element.style.display === 'none') ? 'block' : 'none';
    }
});

    spanElements.forEach(function (element) {
        element.style.display = (element.style.display === 'none') ? 'inline' : 'none';
    });

    
    var submitButton = document.getElementById('submitButton');

    submitButton.style.display = (submitButton.style.display === 'none') ? 'block' : 'none';
}

    var imagesArray = <?php if(!is_null($imagesArray)) {echo json_encode($imagesArray);} ?>;
    var currentIndex = 0;

    function openPreview(index) {
        var modal = document.getElementById('previewModal');
        var modalContent = document.getElementById('modalContent');
        modal.style.display = 'block';

        
        modalContent.src = imagesArray[index];
        currentIndex = index;

        
        window.addEventListener('click', function (event) {
      if (event.target === modal) {
        closePreview();
      }
    });

        
        window.addEventListener('keydown', handleArrowKeys);
    }

    function closePreview() {
        var modal = document.getElementById('previewModal');
        modal.style.display = 'none';

        
        window.onclick = null;

        
        window.removeEventListener('keydown', handleArrowKeys);
    }

    function handleArrowKeys(event) {
        if (event.key === 'ArrowLeft') {
            
            currentIndex = (currentIndex - 1 + imagesArray.length) % imagesArray.length;
            document.getElementById('modalContent').src = imagesArray[currentIndex];
        } else if (event.key === 'ArrowRight') {
            
            currentIndex = (currentIndex + 1) % imagesArray.length;
            document.getElementById('modalContent').src = imagesArray[currentIndex];
        }
    }
     function fileUpload() {
    var fileLabel = document.getElementById('uploadLabel');
    var fileInput = document.getElementById('profilepic');
    var MedicalLbl = document.getElementById('uploadMedical');
    var MedicalIn = document.getElementById('mdeical');

    fileLabel.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function (event) {
        
        console.log('File selected:', event.target.files[0]);
    });
    MedicalLbl.addEventListener('click', function () {
        MedicalIn.click();
    });

    MedicalIn.addEventListener('change', function (event) {
        
        console.log('File selected:', event.target.files[0]);
    });
}    
    fileUpload();
    function printImage() {
    var modalContent = document.getElementById('modalContent');
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<img src="' + modalContent.src + '" style="min-width: 100%; margin:0px;">');
    printWindow.document.close();
    printWindow.print();
}

</script>

</body>

</html>