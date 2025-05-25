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

$hospitalId = $_SESSION['hospital_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patientId'];

    
    $bloodReport = $_FILES['bloodResult'];
    $blood = handleFileUpload($bloodReport);

    
    $xrayReport = $_FILES['xrayResult'];
    $xray = handleFileUpload($xrayReport);

    
    $videoXrayReport = $_FILES['videoXrayResult'];
    $videoXray = handleFileUpload($videoXrayReport);

    
    $otherReport = $_FILES['otherResult'];
    $other = handleFileUpload($otherReport);

    

    $query = 'INSERT INTO lab_result (bloodReport, xrayReport, videoXrayReport, otherReport, patient_id) VALUES (?,?,?,?,?)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $blood, $xray, $videoXray, $other, $patient_id);
    $stmt->execute();

    header("Location: dashboard.html");
    exit();
}

function handleFileUpload($file)
{
    if ($file['error'] == UPLOAD_ERR_OK) {
        $uploadDirectory = "C:/xampp/htdocs/pic/";
        $targetPath = "pic/" . basename($file['name']);
        $filePath = $uploadDirectory . basename($file['name']);

        move_uploaded_file($file['tmp_name'], $filePath);
    }
    return $targetPath;
}
?>



<style>
   body {
    font-family: Arial, sans-serif;
  }

  #main-container {
    border: 2px solid blue;
    padding: 20px;
    margin-top: 20px;
    text-align: center; 
  }

  #patientId {
    display: block;
    margin: 20px auto;
    padding: 8px;
    border: 2px solid red;
    width: 200px;
    text-align: center;
    font-size: 16px;
  }

  #picture-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-gap: 20px;
    margin: 10px;
  }

  .picture-item {
    border: 2px solid black;
    padding: 10px;
    border-radius: 8px;
  }

  .picture-item label {
    margin-bottom: 5px;
    display: block;
  }

  .picture-item input[type="file"] {
    display: none;
  }

  .picture-item .file-upload {
    border: 2px solid red;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    color: #0066cc;
    text-align: center;
  }

  .picture-item img {
    max-width: 150px;
    max-height: 150px;
    margin-top: 10px;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  #submit-button {
    display: block;
    margin: 0 auto; 
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #0066cc;
    color: white;
    font-size: 16px;
    cursor: pointer;
  }
</style>
<form method="POST" action="labresult.php" enctype="multipart/form-data">

  <div id="main-container">
    <input type="text" id="patientId" name="patientId" placeholder="Enter Patient ID" required>
  
    <div id="picture-container">
      <div class="picture-item">
        <label for="bloodResult">Blood Test Result (JPG/PNG):</label>
        <input type="file" id="bloodResult" name="bloodResult" accept=".jpg, .png">
        <label class="file-upload" for="bloodResult">Upload Blood Result</label>
        <img id="bloodResultPreview" src="" alt="Blood Result Preview">
      </div>
  
      <div class="picture-item">
        <label for="xrayResult">X-Ray Result (JPG/PNG):</label>
        <input type="file" id="xrayResult" name="xrayResult" accept=".jpg, .png">
        <label class="file-upload" for="xrayResult">Upload X-Ray Result</label>
        <img id="xrayResultPreview" src="" alt="X-Ray Result Preview">
      </div>
      <div class="picture-item">
        <label for="VideoXrayResult">Video X-Ray Result (JPG/PNG):</label>
        <input type="file" id="VideoXrayResult" name="videoXrayResult" accept=".jpg, .png">
        <label class="file-upload" for="VideoXrayResult">Upload Video X-Ray Result</label>
        <img id="VideoxrayResultPreview" src="" alt="video X-Ray Result Preview">
    </div>
    <div class="picture-item">
        <label for="OtherResult">Other Result (JPG/PNG):</label>
        <input type="file" id="OtherResult" name="otherResult" accept=".jpg, .png">
        <label class="file-upload" for="OtherResult">Other Result</label>
        <img id="OtherResultPreview" src="" alt="Other Result Preview">
    </div>`
  
  </div>
  <button id="submit-button" type="submit">Submit</button>
</form>

<script>
  
  document.getElementById('bloodResult').addEventListener('change', function(event) {
    var img = document.getElementById('bloodResultPreview');
    img.src = URL.createObjectURL(event.target.files[0]);
  });

  document.getElementById('xrayResult').addEventListener('change', function(event) {
    var img = document.getElementById('xrayResultPreview');
    img.src = URL.createObjectURL(event.target.files[0]);
  });
  document.getElementById('VideoXrayResult').addEventListener('change', function(event) {
    var img = document.getElementById('VideoxrayResultPreview');
    img.src = URL.createObjectURL(event.target.files[0]);
  });
  document.getElementById('OtherResult').addEventListener('change', function(event) {
    var img = document.getElementById('OtherResultPreview');
    img.src = URL.createObjectURL(event.target.files[0]);
  });
</script>
