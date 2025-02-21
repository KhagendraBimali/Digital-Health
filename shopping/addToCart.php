<?php
session_start();

if (isset($_GET['productId']) && isset($_GET['name']) && isset($_GET['price'])) {
  $productId = $_GET['productId'];
  $productName = $_GET['name'];
  $productPrice = $_GET['price'];
  $imagePath = $_GET['imagePath'];
  $hospitalId =$_GET['hospital_id'];
  $quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;

  
  $productKey = 'product_' . $productId;

  
  if ($quantity > 0) {
    if (!isset($_SESSION['cart'][$productKey])) {
      $_SESSION['cart'][$productKey] = [
        'id' => $productId,
        'name' => $productName,
        'price' => $productPrice,
        'image' => $imagePath,
        'hospital_id'=> $hospitalId,
        'quantity' => $quantity,
        
      ];
    } else {
      
      $_SESSION['cart'][$productKey]['quantity'] = $quantity;
    }
  } else {
    
    unset($_SESSION['cart'][$productKey]);
  }
}
?>
