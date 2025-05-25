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

if(isset($_GET['total_amount'])){
    $_SESSION['total_amount']=(float)urldecode($_GET['total_amount']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $order_date = date("Y-m-d");    
    $total_amount = $_SESSION['total_amount'];
    $status = 'Pending';
    $hospital_id = null;
    $order_id = null;
    
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productKey => $productDetails) {
            if (is_array($productDetails)) {
                $product_hospital_id = $productDetails['hospital_id'];
                
                if ($hospital_id === null || $hospital_id !== $product_hospital_id) {
                    $query = "INSERT INTO orders (full_name, phone, address, order_date, total_amount, status, hospital_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssssdss", $full_name, $phone, $address, $order_date, $total_amount, $status, $product_hospital_id);
                    $stmt->execute();
                    
                    $order_id = $conn->insert_id;
                    
                    $hospital_id = $product_hospital_id;
                }
                
                $total = $productDetails['price'] * $productDetails['quantity'];
                $query = "INSERT INTO order_items (order_id, product_id, quantity, total) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iiid", $order_id, $productDetails['id'], $productDetails['quantity'], $total);
                $stmt->execute();
                
                $productId = $productDetails['id'];
                $sql ="SELECT stock FROM products WHERE product_id=?";
                $stmtStock = $conn->prepare($sql);
                $stmtStock->bind_param("i", $productId);
                $stmtStock->execute();
                $result = $stmtStock->get_result();
                $row = $result->fetch_assoc();
                $currentStock = $row['stock'];
                $newStock = $currentStock - $productDetails['quantity'];
                
                $sql1 = "UPDATE products SET stock = ? WHERE product_id=?";
                $stmtStock1 = $conn->prepare($sql1);
                $stmtStock1->bind_param("ii", $newStock, $productId);
                $stmtStock1->execute();
            }
        }
    }
    header("Location: index.php");
    exit();
}
?>

<head>
<link rel="stylesheet" type="text/css" href="../../patient_signup.css">
<style>
    .error {
    color: red;
    display: block;
    margin-top: 5px;
    }
</style>
<script>
        function validateForm() {
            var fullName = document.getElementById('full_name').value;
            var phone = document.getElementById('phone').value;
            var address = document.getElementById('address').value;

            
            var errors = document.getElementsByClassName('error');
            for (var i = 0; i < errors.length; i++) {
                errors[i].innerText = '';
            }

            var isValid = true;

            
            if (fullName === '') {
                document.getElementById('full_name_error').innerText = 'Name cannot be blank';
                isValid = false;
            }

            
            var phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phone)) {
                document.getElementById('phone_error').innerText = 'Phone number must be exactly 10 digits';
                isValid = false;
            }

            
            if (address === '') {
                document.getElementById('address_error').innerText = 'Address cannot be blank';
                isValid = false;
            }

            return isValid; 
        }
    </script>
</head>
<div class="signup-container">

    <div class="signup-box" style="width:40%">
        <h1>Enter Your Details</h1>
        
        <form id="signupForm" action="checkout.php" method="post" onsubmit="return validateForm()">
            <div class="input-container">
                <input type="text" name="full_name" placeholder="Full Name" id="full_name">
                <span id="full_name_error" class="error"></span>
                <input type="text" name="phone" placeholder="Phone Number" id="phone">
                <span id="phone_error" class="error"></span>
            <input type="text" name="address" placeholder="Address" id="address">
            <span id="address_error" class="error"></span>
        </div>
        <button type="submit">Checkout</button>
    </form>
</div>
<?php  var_dump($_SESSION);

?>

</div>
