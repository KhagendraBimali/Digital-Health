<head><link rel="stylesheet" href="../../snr/table.css">
</head>
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
$query = "SELECT * FROM emergency_services";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo '<h1>Emergency Services</h1>';
    echo '<div id="data">';
    echo '<table id="tab">';
    echo '<tr>';
    echo '<th>S.N.</th>';
    echo '<th>Name</th>';
    echo '<th>Address</th>';
    echo '<th>Phone</th>';
    echo '</tr>';
    $i=1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $i . '</td>';
        echo '<td>'. $row['name'] .'</td>';
        echo '<td>'. $row['address'] .'</td>';
        echo '<td>'. $row['phone'] .'</td>';
        echo '</tr>';
        $i++;
    }
    echo '</table>';
    echo '</div>';
}

?>
