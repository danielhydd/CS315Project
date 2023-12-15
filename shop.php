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

// Check if the "Add to Cart" button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    $item_id = $_POST["item"];

    // Insert the item into the shopping cart table
    $sql = "INSERT INTO shopping_cart (user_id, item_id) VALUES ($user_id, $item_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $item_id);


    if ($stmt->execute()) {
        echo "Item added to cart successfully";
    } else {
        echo "Error adding item to cart: " . $conn->error;
    }
    $stmt->close();
}

// Fetch items from the database
$sql_items = "SELECT * FROM items";
$result_items = $conn->query($sql_items);

$items = array();

if ($result_items->num_rows > 0) {
    while ($row = $result_items->fetch_assoc()) {
        $items[] = $row;
    }
}

// Close the MySQL connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Welcome to our Online Shop!</h2>

    <div class="item-container">
        <?php foreach ($items as $item) : ?>
            <div class="item">
                <img src="item<?php echo $item['item_id']; ?>.jpg" alt="<?php echo $item['name']; ?>">
                <h3><?php echo $item['name']; ?></h3>
                <p>$<?php echo number_format($item['price'], 2); ?></p>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="item" value="<?php echo $item['item_id']; ?>">
                    <button type="submit" class="add-to-cart-btn" name="add_to_cart">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Link to Shopping Cart -->
    <div class="cart-link">
        <a href="cart.php">View Shopping Cart</a>
    </div>

</body>
</html>
