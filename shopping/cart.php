<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart</title>
  <link rel="stylesheet" href="../../snr/table.css">

  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }

    .cart-container {
      max-width: 800px;
      margin: 20px auto;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .Header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .Heading {
      font-size: 24px;
      margin: 0;
    }

    .cartItem {
      margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
    }

    .image-box {
      margin-right: 15px;
      display:flex
    }

    .image-box img {
      max-width: 100%;
      border-radius: 5px;
      height: 120px;
    }

    .product {
      flex-grow: 1;
    }

    .price {
      font-weight: bold;
    }
    .quantity{
      display: flex;
      justify-content: center;
    }

    .cart-total {
      
      font-size: 16px;
      font-weight: bold;
    }

        button {
    display: block; 
    margin: 10px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    
  }
  
  button:hover {
    background-color: #0056b3;
    
  }
  .checkout{
    display: flex;
    justify-content: flex-end;
    
  }
  </style>
</head>
<body>

    <div class="cart-container">
        <div class="Header">
            <h3 class="Heading">Your Cart</h3>
        </div>
        <table id="tab">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="cartItems"></tbody>
            <tfoot>
                <tr>
                    <td class="cart-total" colspan="3">Total:</td>
                    <td>
                        <div class="cart-total" id="cartTotal"></div>
                    </td>
                </tr>
                <tr>
                <td class="cart-total" colspan="3" >13% VAT: </td>
                  <td class="cart-total" id="tax"></td>
                </tr>
                <td class="cart-total" colspan="3">GrandTotal: </td>
                  <td class="cart-total" id="grandTotal"></td>
                </tr>
            </tfoot>
        </table>
        <div class="checkout">
          <button onclick="checkout()">Proceed to Checkout</button>

        </div>
    </div>

  <script>
    
const cartItemsTbody = document.getElementById('cartItems');
const cartTotalDiv = document.getElementById('cartTotal');
const taxDiv = document.getElementById('tax');
const grandTotalDiv = document.getElementById('grandTotal');
function displayCartItems(cartItems) {
    
    cartItemsTbody.innerHTML = '';

    let cartTotal = 0.0 ;
    let tax = 0.0 ;
    let grandTotal = 0.0 ;

    
    if (Array.isArray(cartItems)) {
        cartItems.forEach(item => {
            if (item.quantity > 0) {
                const cartItem = document.createElement('tr');
                cartItem.classList.add('cartItem');

                const imageAndNameTd = document.createElement('td');
                const imageDiv = document.createElement('div');
                imageDiv.className = 'image-box';
                const imagePath = document.createElement('img');
                imagePath.src = item.image;
                imagePath.alt = item.name;
                imageDiv.appendChild(imagePath);
                
                const productName = document.createElement('span');
                productName.textContent = item.name || 'N/A';
                productName.style.alignSelf='center';
                imageDiv.appendChild(productName);
                imageAndNameTd.appendChild(imageDiv);

                const priceTd = document.createElement('td');
                const priceDiv = document.createElement('div');
                priceDiv.className = 'price';
                const productPrice = document.createElement('span');
                productPrice.textContent = `$${item.price}`;
                priceDiv.appendChild(productPrice);
                priceTd.appendChild(priceDiv);

                const quantityTd = document.createElement('td');
                const quantityDiv = document.createElement('div');
                quantityDiv.className = 'quantity';
                const decreaseBtn = document.createElement('button');
                decreaseBtn.textContent = '-';
                decreaseBtn.addEventListener('click', () => updateCartItemQuantity(item.id, item.name, item.price, item.image, item.hospital_id, item.quantity-1));
      
                const quantityInput = document.createElement('span');
                quantityInput.style.padding='20px';
                quantityInput.textContent=item.quantity;
                
                const increaseBtn = document.createElement('button');
                increaseBtn.textContent = '+';
                increaseBtn.addEventListener('click', () => updateCartItemQuantity(item.id, item.name, item.price, item.image, item.hospital_id, parseInt(item.quantity) + 1));
      
                quantityDiv.appendChild(decreaseBtn);
                quantityDiv.appendChild(quantityInput);
                quantityDiv.appendChild(increaseBtn);
                quantityTd.appendChild(quantityDiv);
                
                const totalTd = document.createElement('td');
                const totalDiv = document.createElement('div');
                totalDiv.className = 'total';
                const total = document.createElement('span');
                const totalPrice = item.price * item.quantity;
                total.textContent = `$${totalPrice}`;
                totalDiv.appendChild(total);
                totalTd.appendChild(totalDiv);

                cartItem.appendChild(imageAndNameTd);
                cartItem.appendChild(priceTd);
                cartItem.appendChild(quantityTd);
                cartItem.appendChild(totalTd);

                cartItemsTbody.appendChild(cartItem);
                cartTotal += item.price * item.quantity;
                tax=13/100*cartTotal;
                grandTotal=cartTotal+tax;

            }
        });
    }
    taxDiv.textContent=`$${tax.toFixed(2)}`
    cartTotalDiv.textContent = `$${cartTotal.toFixed(2)}`;
    grandTotalDiv.textContent=`$${grandTotal.toFixed(2)}`;
}

function updateCartItemQuantity(productId, name, price, imagePath, hospital_id, newQuantity) {
    
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `addToCart.php?productId=${productId}&name=${name}&price=${price}&imagePath=${imagePath}&hospital_id=${hospital_id}&quantity=${newQuantity}`, true);
    xhr.send();

    
    fetchCartItems();
}

function fetchCartItems() {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const cartItems = JSON.parse(xhr.responseText);
            displayCartItems(cartItems);
        }
    };
    xhr.open("GET", "getCartItems.php", true);
    xhr.send();
}


window.onload = () => {
    fetchCartItems();
};


function checkout() {
    
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const cartItems = JSON.parse(xhr.responseText);

            
            let queryString = cartItems.map(item => {
                return `productId=${item.id}&name=${encodeURIComponent(item.name)}&price=${item.price*item.quantity}&quantity=${item.quantity}&hospital_id=${item.hospital_id}`;
            }).join('&');

            
            const cartTotal = cartItems.reduce((total, item) => total + item.price * item.quantity, 0);
            const tax = 0.13 * cartTotal;
            const total_amount = cartTotal + tax;
            queryString += `&total_amount=${total_amount}`;
            

            
            window.location.href = `checkout.php?${queryString}`;
        }
    };
    xhr.open("GET", "getCartItems.php", true);
    xhr.send();
}

  </script>
</body>
</html>
