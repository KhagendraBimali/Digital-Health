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
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    $sql = 'UPDATE orders SET status=? where order_id=?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss',$status, $orderId);
    $stmt->execute();
}

$sql = 'SELECT * from orders where hospital_id=?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('s',$hospitalId);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
<head>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../../snr/table.css">
    <link rel="stylesheet" type="text/css" href="../../snr/patient_signup.css">
    <style>
        #tab td {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Orders</h1>
        <div id="data">
            <table id="tab">
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Status</th>
            </tr>
            <?php foreach($orders as $row){
        ?>
        <tr class = "order-row">
            <td><?php echo $row['order_id']?></td>
            <td><?php echo $row['full_name'];?></td>
            <td><?php echo $row['status']?>
            <?php if ($row['status'] === 'Pending'){ ?>
                    <button class="delivered" >Delivered</button>
                
            </td>
        <?php } ?>
        </tr>
        <?php }?>
</body>