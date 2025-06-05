<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Page</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">
        <a href="/project/index.php"><img src="../pic/logod.png" alt="Logo" ></a>
      </div>
      <div class="search-bar">
        <form method="GET" action="index.php">    
          <input type="text" name="search" placeholder="Search...">
          <button type="submit">Search</button>
        </form>
      </div>
      <div class="cart-icon">
  <a href="/project/shopping/cart.php"><img src="../pic/cart.png" alt="Cart"></a>
  <span id="cart-count">0</span> 
</div>
    </nav>
  </header>

  <main class="main">
  <div class="sorting-options">
      <form method="GET" action="index.php">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
          <option value="high-low">High to Low</option>
          <option value="low-high">Low to High</option>
        </select>
        <button type="submit">Sort</button>
      </form>
    </div>
    <br>
    <div class="item-row">
      <?php
      session_start();
      $host = "localhost"; 
      $username = "root"; 
      $password = ""; 
      $database = "hospital";

      $conn = mysqli_connect("localhost", "root", "", "hospital");

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      
      $sql = "SELECT p.*, h.* FROM products p left join admin_signup h on h.hospital_id = p.hospital_id where stock > 0";
      $result = $conn->query($sql);

      
      $products = [];
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $products[] = $row;
        }
      }

     
function quickSortByPrice(&$products, $left, $right) {
  if ($left < $right) {
    $pivot = partition($products, $left, $right);
    quickSortByPrice($products, $left, $pivot - 1);
    quickSortByPrice($products, $pivot + 1, $right);
  }
}


function partition(&$products, $left, $right) {
  $pivot = $products[$right]['price'];
  $i = $left - 1;

  for ($j = $left; $j < $right; $j++) {
    if ($products[$j]['price'] < $pivot) {
      $i++;
      
      $temp = $products[$i];
      $products[$i] = $products[$j];
      $products[$j] = $temp;
    }
  }

  
  $temp = $products[$i + 1];
  $products[$i + 1] = $products[$right];
  $products[$right] = $temp;

  return $i + 1;
}





if (isset($_GET['sort'])) {
  $sortOrder = $_GET['sort'];

  
  if ($sortOrder === 'high-low') {
    quickSortByPrice($products, 0, count($products) - 1);
    $products = array_reverse($products);
  } elseif ($sortOrder === 'low-high') {
    quickSortByPrice($products, 0, count($products) - 1);
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

      
      if (isset($_GET['search'])) {
        $searchTerm = $_GET['search'];
        $searchResults = searchProducts($searchTerm, $products);

        
        if (!empty($searchResults)) {
          foreach ($searchResults as $result) {
            echo '<div class="item">';
            echo '<div class="product-box">';
            echo '<img src="' . $result["image_path"] . '" alt="no pic' . $result["name"] . '">';
            echo '<p class="product-name">' . $result["name"] . '</p>';
            echo '<p class="product-name"> By ' . $result["full_name"] . '</p>';
            echo '<p class="product-price">$' . $result["price"] . '</p>';
            echo '<div class="buttons">';
            echo '<a href="/project/shopping/cart.php"><button class="buy-now" onclick="addToCart(' . $result["product_id"] . ',\''.$result["name"].'\',' . $result["price"] .',\' '. $result["image_path"].'\', '. $result['hospital_id'].')">Buy Now</button></a>';
            echo '<button class="add-to-cart" onclick="addToCart(' . $result["product_id"] . ',\''.$result["name"].'\',' . $result["price"] .',\'' .$result["image_path"].'\','. $result['hospital_id'].')">Add to Cart </button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          echo "No products found.";
        }
      } else {
        
        foreach ($products as $row) {
          echo '<div class="item">';
          echo '<div class="product-box">';
          echo '<img src="' . $row["image_path"] . '" alt="no pic' . $row["name"] . '">';
          echo '<p class="product-name">' . $row["name"] . '</p>';
          echo '<p class="product-name">' . $row["full_name"] . '</p>';
          echo '<p class="product-price">$' . $row["price"] . '</p>';
          echo '<div class="buttons">';
          echo '<a href="/project/shopping/cart.php"><button class="buy-now" onclick="addToCart(' . $row["product_id"] . ',\'' . $row["name"] . '\',' . $row["price"] . ',\' '. $row["image_path"].'\', '. $row['hospital_id'].')">Buy Now</button></a>';
          echo '<button class="add-to-cart" onclick="addToCart(' . $row["product_id"] . ',\'' . $row["name"] .'\',' . $row["price"] . ',\' '. $row["image_path"].'\', '. $row['hospital_id'].')">Add to Cart </button>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
        }
      }
     
      
      $conn->close();
      ?>
    </div>
  </main>
  <?php include "../../project/footer.html";?>
</body>
</html>


<script>
    
    function addToCart(productId, name, price, imagePath, hospital_id) {
      
      
      const xhr = new XMLHttpRequest();
      xhr.open("GET", "addToCart.php?productId=" + productId + "&name=" + name + "&price=" + price + "&imagePath=" + imagePath + "&hospital_id=" + hospital_id, true);
      xhr.send();

      
      const cartCount = document.getElementById('cart-count');
      let itemCount = parseInt(cartCount.textContent);
      itemCount++;
      cartCount.textContent = itemCount;
      cartCount.style.display = 'inline-block';
      cartCount.classList.add('bump');
      setTimeout(() => {
        cartCount.classList.remove('bump');
      }, 300);
    }
  </script>

