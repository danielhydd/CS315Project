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

$user_id = $_SESSION["user_id"];

// Retrieve items from the shopping cart for the current user
$sql = "SELECT shopping_cart.cart_id, items.item_id, items.name, items.price, shopping_cart.quantity
        FROM shopping_cart
        INNER JOIN items ON shopping_cart.item_id = items.item_id
        WHERE shopping_cart.user_id = $user_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total cost, tax, and grand total
$total_cost = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_cost += $row['price'];
    }
}



$tax_rate = 0.1; // You can set your desired tax rate here
$tax_amount = $total_cost * $tax_rate;
$grand_total = $total_cost + $tax_amount;

// Insert order information into user_orders
$order_date = date("Y-m-d H:i:s"); // Current date and time
$sql_insert_order = "INSERT INTO user_orders (user_id, order_date, total_cost, tax_amount, grand_total)
                    VALUES ($user_id, '$order_date', $total_cost, $tax_amount, $grand_total)";

if ($conn->query($sql_insert_order) === TRUE) {
    $order_id = $conn->insert_id; // Get the last inserted order_id

    

    // Clear the shopping cart for the user
    $sql_clear_cart = "DELETE FROM shopping_cart WHERE user_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param("i", $user_id);
    $stmt_clear_cart->execute();

    echo "Order placed successfully!";
    header("Location: shop.php");
    exit();
} else {
    echo "Error placing order: " . $stmt_insert_order->error;
}

// Close the MySQL connection
$conn->close();
?>
