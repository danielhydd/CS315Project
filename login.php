<?php
session_start();

// Connect to MySQL
$conn = new mysqli("localhost", "danielhydd", "daniel0405", "user_info");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Retrieve user from the database using prepared statement
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            // Set session variables and redirect to a logged-in page
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            header("Location: about.html");
            exit();
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the MySQL connection
$conn->close();
?>

<html>
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
  </nav>
</div>
    </div>
<body>
    <h2>User Login</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
