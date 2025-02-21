<?php
session_start();
error_log(print_r($_SESSION['cart'], true));

if (isset($_SESSION['cart'])) {
  
  echo json_encode(array_values($_SESSION['cart']));
} else {
  
  echo json_encode([]);
}
?>
