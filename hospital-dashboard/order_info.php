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

$orderId = $_GET['order_id'];
$sql = 'SELECT * from orders o JOIN order_items ot on ot.order_id = o.order_id
    JOIN products p ON p.product_id = ot.product_id
    where o.order_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $orderId);
$stmt->execute();
$result = $stmt->get_result();

$orderDetails = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<head>
    <link rel="stylesheet" href="../../table.css">
    <style>
        #back {
            margin: 16px;
            align-self: center;
            max-width: 100%;
            max-height: 20px;
            border-radius: 30%;
        }

        #back:hover {
            background-color: #f9f9f9;
        }
        h3 {
            text-align: center;
        }
        h4 {
            margin-top: 2px;
            margin-bottom: 2px ;
        }
        #information {
            margin-right: 200px;
            margin-left: 200px;
        }
        .oid, .add {
            display: flex;
            justify-content: space-between;
        }
        #printButton {
            top: 10px;
            right: 10px;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
          }
          #printButton:hover{
            background-color: #0056b3;            
          }

    </style>
    <script src="https:
    <script>
        $(document).ready(function () {
            $('#back').on('click', function () {
                $.ajax({
                    url: 'orders.php?order_id=<?php echo $orderId; ?>', 
                    type: 'GET',
                    success: function (data) {
                        $('#main-content').html(data); 
                    },
                    error: function () {
                        $('#main-content').html('Failed to load orders.');
                    }
                });
            });
            $('#printButton').on('click', function () {
                var printWindow = window.open('', '_blank');
                var printContents = document.getElementById('info').innerHTML;
                var originalContents = document.body.innerHTML;
                printWindow.document.write('<link rel="stylesheet" href="../../table.css">');
                printWindow.document.write('<style>h3 { text-align: center;}h4 {margin-top: 2px;margin-bottom: 2px ;}#information {margin-right: 200px;margin-left: 200px;}.oid, .add {display: flex;justify-content: space-between;}</style>');
                    printWindow.document.write(printContents);
                    printWindow.document.close();
                printWindow.print();
                printWindow.close();
            });
        });
    </script>
</head>
<body>
    <div style="display:flex; justify-content:space-between;">
        <img id="back" src="../pic/back.png" alt="back">
        <button id="printButton">Print</button>
    </div>
    <div id="info">
        <h1>Order Details</h1>
        <div id="information">
            <div class="oid">
                <h4><strong>Order ID: </strong><?php echo $orderDetails[0]['order_id'] ?></h4>
                <h4><strong>Date: </strong><?php echo $orderDetails[0]['order_date'] ?></h4>
            </div>
                <h4><strong>Name: </strong><?php echo $orderDetails[0]['full_name'] ?></h4>
            <div class="add">
                <h4><strong>Address: </strong><?php echo $orderDetails[0]['address']?></h4>
                <h4><strong>Phone: </strong><?php echo $orderDetails[0]['phone'] ?></h4>
            </div>
        </div>
        <div>
            <h3>Ordered Items</h3>
        </div>
        <div>
            <table id="tab">
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($orderDetails as $row) {?>
                    <tr>
                        <td><?php echo $row['product_id']?></td>
                        <td><?php echo $row['name']?></td>
                        <td> $ <?php echo $row['price']?></td>
                        <td><?php echo $row['quantity']?></td>
                        <td> $ <?php echo $row['total']?></td>
                    </tr>
                <?php }?>
                <tr>
                    <td colspan="4" style="text-align:right;">Grand Total</td>
                    <td> $ <?php echo $orderDetails[0]['total_amount']?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
