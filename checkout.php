<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Connect to MySQL
$conn = new mysqli("localhost", "danielhydd", "daniel0405", "user_info");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$empty = true;
$user_id = $_SESSION["user_id"];

// Retrieve items from the shopping cart for the current user
$sql = "SELECT items.price, shopping_cart.quantity
        FROM shopping_cart
        INNER JOIN items ON shopping_cart.item_id = items.item_id
        WHERE shopping_cart.user_id = $user_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$grand_total = 0;

// Calculate grand total
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grand_total += $row['price']* (1 + 0.10);
    }
}

if($grand_total != 0)
{
    $empty = false;
}
else
{
    $empty = true;
}
// Close the MySQL connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">

    
    <script>
        function validateForm() {
            var creditCard = document.getElementById("credit_card").value;
            var expirationDate = document.getElementById("expiration_date").value;
            var cvv = document.getElementById("cvv").value;
            var shippingAddress = document.getElementById("shipping_address").value;

            // Credit Card Number validation
            if (!/^\d{16}$/.test(creditCard)) {
                alert("Invalid Credit Card Number. Please enter a 16-digit number.");
                return false;
            }

            // Expiration Date validation
            if (!/^\d{2}\/\d{2}$/.test(expirationDate)) {
                alert("Invalid Expiration Date. Please enter in the format MM/YY.");
                return false;
            }

            // CVV validation
            if (!/^\d{3}$/.test(cvv)) {
                alert("Invalid CVV. Please enter a 3-digit number.");
                return false;
            }

            // Shipping Address validation
            if (shippingAddress.trim() === "") {
                alert("Shipping Address cannot be empty.");
                return false;
            }

            // If all validations pass, return true
            return true;
        }
    </script>
    <div class = "head">
      <div class = "navText">
    <nav>
    <a href="home.html" class = "navText">Home</a>
      <a href="tour.html" class = "navText">Tour</a>
      <a href="store.html" class = "navText">Contact Us</a>
      <a href="news.html" class = "navText">News</a>
      <a href="about.html" class = "navText">About</a>
      <a href="shop.php" class = "navText">Shop</a>
      <a href="cart.php" class = "navText">Cart</a>
      <a href="checkout.php" class = "navText">Checkout</a>
      <a href="accountinfo.php" class = "navText">Account</a>
      <a href="login.php" class = "navText">Login</a>
      <a href="register.php" class = "navText">Signup</a>
</div>
    </div>
</head>
<body>

    <h2>Checkout</h2>
    <form method="post" action="process_checkout.php" onsubmit="return validateForm()">
        <!-- Credit Card Details -->
        <label for="credit_card">Credit Card Number:</label>
        <input type="text" id="credit_card" name="credit_card" required>
        <br>

        <label for="expiration_date">Expiration Date:</label>
        <input type="text" id="expiration_date" name="expiration_date" required>
        <br>

        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" required>
        <br>

        <!-- Shipping Address -->
        <label for="shipping_address">Shipping Address:</label>
        <textarea id="shipping_address" name="shipping_address" required></textarea>
        <br>

        <?php if (!$empty): ?>
            <!-- Submit Button -->
            <input type="submit" value="Place Order">
        </form>
        <?php else: ?>
            <p>Your shopping cart is empty. Add items before placing an order.</p>
        <?php endif; ?>
        
    </form>
    <?php
    if ($result->num_rows > 0) {
        echo '<div class="checkout-summary">';
        while ($row = $result->fetch_assoc()) {
            echo '<p><strong>Item:</strong> $' . number_format($row['price'], 2) . ' x ' . $row['quantity'] . '</p>';
        }
        echo '</div>';
    } else {
    }
    ?>

    <!-- Display grand total -->
    <p class="grand-total">Grand Total: $<?php echo number_format($grand_total, 2); ?></p>

    <!-- Additional checkout information and form go here -->

    <!-- Link back to the shop -->
    <div class="back-link">
        <a href="shop.php">Back to Shop</a>
    </div>

</body>
</html>
