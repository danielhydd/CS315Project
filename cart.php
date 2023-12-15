<?php
header("Cache-Control: no-cache");
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

$user_id = $_SESSION["user_id"];

// Handle item removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_item"])) {
    $cart_id = $_POST["cart_id"];

    // Remove the item from the shopping cart using prepared statement
    $sql_remove = "DELETE FROM shopping_cart WHERE cart_id = ?";
    $stmt_remove = $conn->prepare($sql_remove);
    $stmt_remove->bind_param("i", $cart_id);

    if ($stmt_remove->execute()) {
        header("Location: cart.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error removing item from cart: " . $stmt_remove->error;
    }

    // Close the prepared statement
    $stmt_remove->close();
}

// Retrieve items from the shopping cart for the current user
$sql = "SELECT shopping_cart.cart_id, items.name, items.price, shopping_cart.quantity
        FROM shopping_cart
        INNER JOIN items ON shopping_cart.item_id = items.item_id
        WHERE shopping_cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_cost = 0;
// Close the MySQL connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">

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

    <h2>Your Shopping Cart</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="cart-item">';
            echo '<p><strong>Item:</strong> ' . $row['name'] . '</p>';
            echo '<p><strong>Price:</strong> $' . number_format($row['price'], 2) . '</p>';
            echo '<p><strong>Quantity:</strong> ' . $row['quantity'] . '</p>';
            $total_cost += ($row['price']);
             // Form for removing the item
            echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
            echo '<input type="hidden" name="cart_id" value="' . $row['cart_id'] . '">';
            echo '<button type="submit" class="remove-btn" name="remove_item">Remove</button>';
            echo '</form>';
            echo '</div>';
        }

        // Calculate tax (assuming 10% tax rate)
        $tax_rate = 0.10;
        $tax_amount = $total_cost * $tax_rate;

        // Calculate grand total
        $grand_total = $total_cost + $tax_amount;

        // Display total cost, tax, and grand total
        echo '<p class="total-cost">Total Cost: $' . number_format($total_cost, 2) . '</p>';
        echo '<p class="total-cost">Tax (10%): $' . number_format($tax_amount, 2) . '</p>';
        echo '<p class="grand-total">Grand Total: $' . number_format($grand_total, 2) . '</p>';
    } else {
        echo '<p>Your shopping cart is empty.</p>';
    }
    ?>

<div class="checkout-link">
    <a href="checkout.php">Proceed to Checkout</a>
</div>
    <!-- Link back to the shop -->
    <div class="back-link">
        <a href="shop.php">Back to Shop</a>
    </div>

</body>
</html>
