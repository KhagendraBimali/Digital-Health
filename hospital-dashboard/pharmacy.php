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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['Price'];
    $quantity = $_POST['quantity'];
    $imagePath = $_POST['image_path'];
    
    $sql = 'INSERT INTO products(name, price, stock, image_path, hospital_id) values(?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss',$name, $price, $quantity, $imagePath, $hospitalId);
    $stmt->execute();
}
$query = 'SELECT * FROM products where hospital_id = ?';
$stmt = $conn->prepare($query);
$stmt->bind_param('s',$hospitalId);
$stmt->execute();
$result = $stmt->get_result();
$products= [];
if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
function searchProducts($searchTerm, $products) {
    $searchResults = [];
    foreach ($products as $product) {
        if (stripos($product['name'], $searchTerm) !== false) {
            $searchResults[] = $product;
        }
    }
    return $searchResults;
}
?>
<head>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../../table.css">
    <link rel="stylesheet" type="text/css" href="../../patient_signup.css">
    <style>

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

        .product {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }
        img{
            width:10%
        }
        .signup-container {
            margin-top: 0;
        }
        #tab td{
            text-align: center;
        }
        
    </style>
<script>
          $(document).ready(function() {
            $('#search-form').submit(function(event) {
                event.preventDefault();

                var searchTerm = $('input[name="search"]').val();

                $.ajax({
                    type: 'GET',
                    url: 'pharmacy.php',
                    data: { search: searchTerm },
                    success: function(response) {
                        $('#main-content').html(response);
                    }
                });
            });
        });
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
        document.getElementById('name_error').textContent = '';
        document.getElementById('price_error').textContent = '';
        document.getElementById('quantity_error').textContent = '';
        document.getElementById('img_error').textContent = '';

        var name = document.getElementsByName('name')[0].value.trim();
        var price = document.getElementsByName('price')[0].value.trim();
        var quantity = document.getElementsByName('quantity')[0].value.trim();
        var image = document.getElementById('pic').files[0];
        var isValid = true;

        if (name === '') {
            document.getElementById('name_error').textContent = 'Please enter a name';
            isvalid= false;
        }

        if (price < 0 || isNaN(parseFloat(price))) {
            document.getElementById('price_error').textContent = 'Please enter a valid price';
            isvalid= false;
        }

        if (quantity < 0 || isNaN(parseInt(quantity))) {
            document.getElementById('quantity_error').textContent = 'Please enter a valid quantity';
            isvalid= false;
        }

        if (!image) {
            document.getElementById('img_error').textContent = 'Please select an image';
            isvalid = false;
        }

        return isvalid;
    }
</script>
</head>
<body>
    <div id="viewForm" class="form-section">
        <h1>Add new Medicine</h1>
        <div class="signup-container">
            <div class="right-section">
                <div class="signup-box">
                    <form action="pharmacy.php" method="post">
                        <div class="input-container">
                            <input type="text" name="name" placeholder="Name" >
                            <span id="name_error" class="error"></span>
                            
                            <input type="text" name="price" placeholder="Price" >
                            <span id="price_error" class="error"></span>
                            
                            <input type="text" name="quantity" placeholder="Quantity" >
                            <span id="quantity_error" class="error"></span>
                            
                            <input type="file" id="pic" name="image_path"  accept="image/*">
                            <br><span id="img_error" class="error"></span>
                        </div>
                        <div style="display:flex;">
                            <button type="button" onclick="showForm('book')">Back</button>
                            <button type="submit" onclick="return validateForm()">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="bookForm" class="form-section active">
        <h1>Medicine Stock</h1>
        <div id ="search-bar" class="search-bar"style="display:flex; justify-content:space-between; margin-bottom: 10px;">
            <form id="search-form">
                <input type="text" name="search" placeholder="Search...">
                <button type="submit">Search</button>
            </form>
            <button onclick="showForm(`view`)">Add new Medicine</button>
        </div>
         <div id="data">
            <table id="tab">
                <tr>
                    <th>Product ID</th>
                    <th>Product </th>
                    <th>Price</th>
                    <th>Quantity</th>
                    
                </tr>
                <?php if(isset($_GET['search'])){
                    $searchTerm = $_GET['search'];
                    $searchResults = searchProducts($searchTerm, $products);
                    if(!empty($searchResults)){
                        foreach($searchResults as $row){
                ?>
                <tr>
                    <td><?php echo $row['product_id']?></td>
                    <td><div class="product"><img src="<?php echo $row['image_path']?>"> <?php echo $row['name']?><div></td>
                    <td><?php echo $row['price'];?></td>
                    <td><?php echo $row['stock']?></td>
                </tr>
                <?php }}} else if(!is_null($products)){
                    foreach($products as $row){?>
                                    <tr>
                    <td><?php echo $row['product_id']?></td>
                    <td><div class="product"><img src="<?php echo $row['image_path']?>"> <?php echo $row['name']?><div></td>
                    <td><?php echo $row['price'];?></td>
                    <td><?php echo $row['stock']?></td>
                </tr>
                <?php }}?>

            </table>
    </div>
</body>