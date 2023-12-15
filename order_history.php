<?php
session_start();

// Check if the user is not logged in, redirect to login page
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

// Retrieve order history for the current user
$sql = "SELECT order_id, order_date, total_cost, tax_amount, grand_total
        FROM user_orders
        WHERE user_id = $user_id
        ORDER BY order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Close the prepared statement
$stmt->close();

// Close the MySQL connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="style.css">
    <!-- Add any additional styles as needed -->
</head>
<body>

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

<h2>Order History</h2>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="order-item">';
        echo '<p><strong>Order ID:</strong> ' . $row['order_id'] . '</p>';
        echo '<p><strong>Order Date:</strong> ' . $row['order_date'] . '</p>';
        echo '<p><strong>Total Cost:</strong> $' . number_format($row['total_cost'], 2) . '</p>';
        echo '<p><strong>Tax Amount:</strong> $' . number_format($row['tax_amount'], 2) . '</p>';
        echo '<p><strong>Grand Total:</strong> $' . number_format($row['grand_total'], 2) . '</p>';
        echo '</div>';
    }
} else {
    echo '<p>No order history found.</p>';
}
?>

<!-- Link back to accountinfo.php -->
<div class="back-link">
    <a href="accountinfo.php">Back to Account Info</a>
</div>

</body>
</html>
