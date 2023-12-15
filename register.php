<?php
session_start(); // Add this line
// Connect to MySQL
$conn = new mysqli("localhost", "danielhydd", "daniel0405", "user_info");
$username_error = false;
$pwd_error = false;

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if username and password are not blank
    if (empty($username) || empty($password)) {
        echo "Both username and password are required.";
        $username_error = true;
        $pwd_error = true;
    } else {
        // Check if the username already exists
        // Check if the username already exists using prepared statement
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "Username already exists";
            $username_error = true;
        } else {
            // Insert new user into the database using prepared statement
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $username, $password_hash);

            if ($insert_stmt->execute()) {
                echo "User registered successfully";
            } else {
                echo "Error: " . $insert_stmt->error;
            }
        }
    }
}

if (isset($check_stmt)) {
    $check_stmt->close();
}
if (isset($insert_stmt)) {
    $insert_stmt->close();
}
// Close the MySQL connection
$conn->close();
?>

<html>
<link rel="stylesheet" href="style.css">
<div class = "head">
<style>
        .error {
            color: red;
        }
    </style>
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
<body>
    <h2>User Registration</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label <?php if ($username_error) echo 'class="error"'; ?>>Username:</label>
        <input type="text" name="username"><br>
        
        <label <?php if ($pwd_error) echo 'class="error"'; ?>>Password:</label>
        <input type="password" name="password"><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
