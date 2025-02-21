<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php
echo '<div id="container">';
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hospital';


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$patient_id = $_SESSION['patient_id'];
$query = 'SELECT * FROM lab_result WHERE patient_id=?';
$stmt = $conn->prepare($query);
$stmt->bind_param('s',$patient_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        echo '<div id="main-container">';
        echo '<div id="picture-container">';
        if($row['bloodReport']!==''){
          echo '<div>';
          echo '<div class="picture-item">';
          echo '<img src="'.$row['bloodReport'].'" alt="Blood Report Image" onclick="openPreview(\'' . $row['bloodReport'] . '\')">';
          echo '</div>';
          echo '<label>Blood Report</label>';
          echo '</div>';
        }
        if($row['xrayReport']!==''){
          echo '<div>';
          echo '<div class="picture-item">';
          echo '<img src="'.$row['xrayReport'].'" alt="Xray Report Image" onclick="openPreview(\'' . $row['xrayReport'] . '\')">';
          echo '</div>';
          echo '<label>Xray Report</label>';
          echo '</div>';
        }
        if($row['videoXrayReport']!==''){
          echo '<div>';  
          echo '<div class="picture-item">';
          echo '<img src="'.$row['videoXrayReport'].'" alt="Video Xray Report Image" onclick="openPreview(\'' . $row['videoXrayReport'] . '\')">';
          echo '</div>';
          echo '<label>Video Xray Report</label>';
          echo '</div>';
        }
        if($row['otherReport']!==''){
          echo '<div>';
          echo '<div class="picture-item">';
          echo '<img src="'.$row['otherReport'].'" alt="Other Report Image" onclick="openPreview(\'' . $row['otherReport'] . '\')">';
          echo '</div>';
          echo '<label>Other Report</label>';
          echo '</div>';
        } else {
          echo 'No Reports yet.';
        }

        echo '</div>';
        echo '</div>';
        echo '    <div id="previewModal">
        <span class="close" onclick="closePreview()">&times;</span>
        <img id="modalContent" src="" alt="Preview">
        <button id="printButton" onclick="printImage()">Print</button>
    </div>
    ';
      }
    }
    echo '</div>';
?>
</body>
<style>
   body {
    font-family: Arial, sans-serif;
  }
  #container {
    max-width: 900px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  } 

  #main-container {
    border: 2px solid blue;
    padding: 20px;
    text-align: center; 
    display: flex;
    flex-wrap: wrap;
    margin: 20px;
  }

  #picture-container {
    display: flex;
    grid-gap: 20px;
    margin: 10px;
    flex-wrap: wrap;
  }

  .picture-item {
    border: 2px solid black;
    padding: 10px;
    border-radius: 8px;
    width: 151px; 
    height: 177px; 
    margin-right: 10px; 
    flex-shrink: 0;
  }

  label {
    
    margin-top: 5px;
    display: block;
    font-weight: bold;
  }


  .picture-item img {
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
          <script>
   var currentIndex = 0;
  var imagesArray = []; 

  function openPreview(imageUrl) {
    var modal = document.getElementById('previewModal');
    var modalContent = document.getElementById('modalContent');
    modal.style.display = 'block';

    
    modalContent.src = imageUrl;

    
    window.addEventListener('click', function (event) {
      if (event.target === modal) {
        closePreview();
      }
    });


  }

    function closePreview() {
        var modal = document.getElementById('previewModal');
        modal.style.display = 'none';

        
        window.onclick = null;

    }

    function printImage() {
    var modalContent = document.getElementById('modalContent');
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<img src="' + modalContent.src + '" style="min-width: 100%; margin:0px;">');
    printWindow.document.close();
    printWindow.print();
}


</script>