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
$username = $_SESSION["username"];

// Handle membership checkbox submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_membership"])) {
    $is_member = isset($_POST["member"]) ? 1 : 0;

    // Store membership status in the database using prepared statement
    $sql = "UPDATE users SET is_member = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $is_member, $user_id);

    if ($stmt->execute()) {
        echo "Membership status updated successfully";
    } else {
        echo "Error updating membership status: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Handle leaving membership button submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["leave_membership"])) {
    // Set is_member to 0 (not a member) using prepared statement
    $sql = "UPDATE users SET is_member = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "Left the membership successfully";
    } else {
        echo "Error leaving membership: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Check if the form is submitted to update the data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm_changes"])) {
    $address = $_POST["address"];
    $dob = $_POST["dob"];

    // Store address and date of birth in the database using prepared statement
    $sql = "UPDATE users SET address = ?, dob = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $address, $dob, $user_id);

    if ($stmt->execute()) {
        echo "Address and Date of Birth updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Retrieve user information from the database
$sql = "SELECT address, dob, is_member FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $address = $row["address"];
    $dob = $row["dob"];
    $is_member = $row["is_member"];
} else {
    $address = "";
    $dob = "";
    $is_member = 0;
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
    <title>Account Info</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

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
      <?php if ($is_member == 1) : ?>
                <a href="members.html" class="navText">Members</a>
            <?php endif; ?>
  </nav>
</div>
    <?php if ($is_member == 1) : ?>
        <h1 class="membership-header">Thank you for being a member!</h1>
    <?php endif; ?>

    <h2>Welcome, <?php echo $username; ?>!</h2>

    <h3>Account Information</h3>

    <p>Username: <?php echo $username; ?></p>
    <!-- Membership Checkbox -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="member">Become a Member:</label>
        <input type="checkbox" id="member" name="member" <?php echo $is_member ? 'checked' : ''; ?>>
        <br>
        <input type="submit" name="submit_membership" value="Submit Membership">
    </form>

    <!-- Leave Membership Button -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" name="leave_membership" value="Leave Membership">
    </form>
    <!-- Order History Button -->
    <a href="order_history.php" class="order-history-btn">Order History</a>

    
