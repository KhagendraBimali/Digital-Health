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

        #img {
            max-width: 800px;
            height: auto;
            display: block;
            margin-bottom: 15px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
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


</style>
<body>
<div style="display:flex; justify-content: space-between">
        <img id="back" src="../pic/back.png" alt="back">
        <h1>Patient Information</h1>
        <div></div>
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
if (!isset($_SESSION['doctor_id'])) {
    header("Location:../../snr/index.php");
    exit();
}

$patientId = $_GET['patient_id'];

$sql = "SELECT * FROM medical_report WHERE patient_id =?";
$sqlstmt = $conn->prepare($sql);
$sqlstmt->bind_param("s", $patientId);
$sqlstmt->execute();
$sqlResullt = $sqlstmt->get_result();
$labQuery = "SELECT * FROM lab_result WHERE patient_id=?";
$labstmt = $conn->prepare($labQuery);
$labstmt->bind_param('s',$patientId);
$labstmt->execute();
$labResult =$labstmt->get_result();
$query = "SELECT * FROM patients_signup WHERE patient_id =?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $patientId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<form>';
        echo '<div id=info>';
        echo '<div id ="information">';
        echo '<label><strong>Name: </strong><h4>' . $row['full_name'] . '</h4></label><br>';
        echo '<label><strong>Age: </strong><h4>' . $row['age'] . '</h4></label><br>';
        echo '<label><strong>Gender: </strong><h4>' . $row['gender'] . '</h4></label><br>';
        echo '<label><strong>Address: </strong><h4>' . $row['address'] . '</h4></label><br>';
        echo '<label><strong>Email: </strong><h4>' . $row['email'] . '</h4></label><br>';
        echo '<label><strong>Medical History: </strong>' . $row['medical_history'] . '</label><br>';
        echo '</div>';
        echo '<div id="profile">';
        echo '<img id="img" src="' . $row['profile'] . '"><br>';
        echo '</div>';
        echo '</div>';
    }

    echo '<label><strong>Medical Records:</strong></label><br>';
    if ($sqlResullt->num_rows > 0) {
        $imagesArray = [];
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
        echo '<label><strong>Lab Reports:</strong></label><br>';
        echo '</div>';
        echo '<div id="previewModal">
        <span class="close" onclick="closePreview()">&times;</span>
        <img id="modalContent" src="" alt="Preview">
        </div>';
    } else {
        echo '<h4>No records found.</h4><br>';
    }
        if($labResult->num_rows>0){
            while($row=$labResult->fetch_assoc()){
                echo '<div id="main-container">';
                echo '<div id="picture-container">';
                if($row['bloodReport']!==''){
                  echo '<div class="content">';
                  echo '<div class="picture-item">';
                  echo '<img src="'.$row['bloodReport'].'" alt="Blood Report Image" onclick="openPreview(\'' . $row['bloodReport'] . '\')">';
                  echo '</div>';
                  echo '<label>Blood Report</label>';
                  echo '</div>';
                }
                if($row['xrayReport']!==''){
                    echo '<div class="content">';
                    echo '<div class="picture-item">';
                  echo '<img src="'.$row['xrayReport'].'" alt="Xray Report Image" onclick="openPreview(\'' . $row['xrayReport'] . '\')">';
                  echo '</div>';
                  echo '<label>Xray Report</label>';
                  echo '</div>';
                }
                if($row['videoXrayReport']!==''){
                    echo '<div class="content">';
                    echo '<div class="picture-item">';
                  echo '<img src="'.$row['videoXrayReport'].'" alt="Video Xray Report Image" onclick="openPreview(\'' . $row['videoXrayReport'] . '\')">';
                  echo '</div>';
                  echo '<label>Video Xray Report</label>';
                  echo '</div>';
                }
                if($row['otherReport']!==''){
                    echo '<div class="content">';
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
            </div>';
              }
        } else {
            echo '<h4>No Reports yet.</h4>';
        }
    echo '</form>';
} else {
    echo 'No personal information found.';
}
$sqlstmt->close();
$stmt->close();
?>
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
    flex-shrink: 0;
  }
  .content{
    display:flex;
    flex-direction: column;
    align-items: center;
  }

  #main-conatiner label {
    
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


</style>

    <script>
        $(document).ready(function () {
            $('#back').on('click', function () {
                
                $.ajax({
                    url: 'patients.php', 
                    type: 'GET',
                    success: function (data) {
                        $('#main-content').html(data); 
                    },
                    error: function () {
                        $('#main-content').html('Failed to load patients.'); 
                    }
                });
            });
        });

        <?php
        if(isset($imagesArray)){
         ?>   
        var imagesArray = <?php echo json_encode($imagesArray); ?>;
        var currentIndex = 0;
        
        function openPreview(index) {
            var modal = document.getElementById('previewModal');
            var modalContent = document.getElementById('modalContent');
            modal.style.display = 'block';
            
            
            modalContent.src = imagesArray[index];
            currentIndex = index;
            
            
            window.onclick = function (event) {
                if (event.target == modal) {
                    closePreview();
                }
            };
            
            
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
    <?php }?>
    </script>
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



</script>

</body>
</html>