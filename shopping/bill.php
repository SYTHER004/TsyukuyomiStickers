<?php
session_start();
//@include 'config.php';
$conn = mysqli_connect('localhost', 'root', '', 'shoppingcart') or die('connection failed');

if (isset($_POST['order_btn'])) {
   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $flat = $_POST['flat'];
   $street = $_POST['street'];
   $city = $_POST['city'];
   $state = $_POST['state'];
   $country = $_POST['country'];
   $pin_code = $_POST['pin_code'];

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart`");
   $price_total = 0;
   if (mysqli_num_rows($cart_query) > 0) {
      while ($product_item = mysqli_fetch_assoc($cart_query)) {
         $product_name[] = $product_item['name'] . ' (' . $product_item['quantity'] . ') ';
         $product_price = number_format($product_item['price'] * $product_item['quantity']);
         $price_total += $product_price;
      };
   };

   $total_product = implode(', ', $product_name);
   $detail_query = mysqli_query($conn, "INSERT INTO `order`(name, number, email, method, flat, street, city, state, country, pin_code, total_products, total_price) VALUES('$name','$number','$email','$method','$flat','$street','$city','$state','$country','$pin_code','$total_product','$price_total')") or die('query failed');

   if ($cart_query && $detail_query) {
      echo "
      <div class='order-message-container'>
      <div class='bill'>
      <div class='message-container'>
         <h3>thank you for shopping!</h3>
         <div class='order-detail'>
            <span>" . $total_product . "</span>
            <span class='total'> total : $" . $price_total . "/-  </span>
         </div>
         <div class='customer-details'>
            <p> your name : <span>" . $name . "</span> </p>
            <p> your number : <span>" . $number . "</span> </p>
            <p> your email : <span>" . $email . "</span> </p>
            <p> your address : <span>" . $flat . ", " . $street . ", " . $city . ", " . $state . ", " . $country . " - " . $pin_code . "</span> </p>
            <p> your payment mode : <span>" . $method . "</span> </p>
            <p>(pay when product arrives)</p>
         </div>
            <a href='allproducts.php' class='btn'>continue shopping</a>
            <a href='#'class='btn' onclick='downloadResume()'>Download Bill</a>
      </div>
      </div>
       
         <script src='https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.js'></script>
            <script>
          function downloadResume() {
            const bill = document.querySelector('.bill');
            alert(generating your bill.....!!!!)
            html2pdf().from(bill).save('bill.pdf');
          }
        </script>
      </div>
      ";
   }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css\style.css">

</head>

<body>

        

   <header class="header">

      <div class="flex">

         <a href="index.php" class="logo">Bake Heaven</a>

         <nav class="navbar">
            <a href="allproducts.php">view products</a>
         </nav>

         <?php

         $select_rows = mysqli_query($conn, "SELECT * FROM `cart`") or die('query failed');
         $row_count = mysqli_num_rows($select_rows);

         ?>

         <a href="cart.php" class="cart"><i class="fa-solid fa-cart-shopping"></i><span><?php echo $row_count; ?></span> </a>

         <div id="menu-btn" class="fas fa-bars"></div>

      </div>

   </header>



   <div class="container">

      <section class="checkout-form">

         <h1 class="heading">complete your order</h1>

         <form action="" method="post">

            <div class="display-order">
               <?php
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart`");
               $total = 0;
               $grand_total = 0;
               if (mysqli_num_rows($select_cart) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                     $total_price = number_format($fetch_cart['price'] * $fetch_cart['quantity']);
                     $grand_total = $total += $total_price;
               ?>
                     <span><?= $fetch_cart['name']; ?>(<?= $fetch_cart['quantity']; ?>)</span>
               <?php
                  }
               } else {
                  echo "<div class='display-order'><span>your cart is empty!</span></div>";
               }
               ?>
               <span class="grand-total"> grand total : $<?= $grand_total; ?>/- </span>
            </div>

            <div class="flex">
               <div class="inputBox">
                  <span>your name</span>
                  <input type="text" placeholder="enter your name" name="name" value="<?php $username = $_SESSION['user_id'];
                     $query = mysqli_query($conn, "SELECT name FROM `user_info` WHERE id = '$username'");
                     $userData = mysqli_fetch_assoc($query);
                     echo isset($userData['name']) ? $userData['name'] : '';?>" required>

               </div>
               <div class="inputBox">
                  <span>your number</span>
                  <input type="number" placeholder="enter your number" name="number" required>
               </div>
               <div class="inputBox">
                  <span>your email</span>
                  <input type="email" placeholder="enter your email" name="email" value="<?php $useremail = $_SESSION['user_id'];
                     $query = mysqli_query($conn, "SELECT email FROM `user_info` WHERE id = '$useremail'");
                     $userData = mysqli_fetch_assoc($query);
                     echo isset($userData['email']) ? $userData['email'] : '';?>" required>
               </div>
               <div class="inputBox">
                  <span>payment method</span>
                  <select name="method">
                     <option value="cash on delivery" selected>cash on devlivery</option>
                     <option value="credit cart">credit cart</option>
                     <option value="paypal">paypal</option>
                  </select>
               </div>
               <div class="inputBox">
                  <span>address line 1</span>
                  <input type="text" placeholder="e.g. flat no." name="flat" required>
               </div>
               <div class="inputBox">
                  <span>address line 2</span>
                  <input type="text" placeholder="e.g. street name" name="street" required>
               </div>
               <div class="inputBox">
                  <span>city</span>
                  <input type="text" placeholder="e.g. mumbai" name="city" required>
               </div>
               <div class="inputBox">
                  <span>state</span>
                  <input type="text" placeholder="e.g. maharashtra" name="state" required>
               </div>
               <div class="inputBox">
                  <span>country</span>
                  <input type="text" placeholder="e.g. india" name="country" required>
               </div>
               <div class="inputBox">
                  <span>pin code</span>
                  <input type="text" placeholder="e.g. 123456" name="pin_code" required>
               </div>
            </div>
            <input type="submit" value="order now" name="order_btn" class="btn">
         </form>

      </section>

   </div>
   <script>
      let menu = document.querySelector('#menu-btn');
      let navbar = document.querySelector('.header .navbar');

      menu.onclick = () => {
         menu.classList.toggle('fa-times');
         navbar.classList.toggle('active');
      };

      window.onscroll = () => {
         menu.classList.remove('fa-times');
         navbar.classList.remove('active');
      };


      document.querySelector('#close-edit').onclick = () => {
         document.querySelector('.edit-form-container').style.display = 'none';
         window.location.href = 'admin.php';
      };
   </script>

</body>

</html>