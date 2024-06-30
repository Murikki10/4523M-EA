<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['dealerID'])) {
    echo "<script>alert('Please log in first.'); window.location.href='loginfrm.php';</script>";
    exit();
}

$dealerID = $_SESSION['dealerID'];

$sql = "SELECT * FROM dealer WHERE dealerID = '$dealerID'";
$result = mysqli_query($conn, $sql);
$dealer = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $password = $_POST['password'];
    $contactNumber = $_POST['contactNumber'];
    $faxNumber = $_POST['faxNumber'];
    $deliveryAddress = $_POST['deliveryAddress'];

    $updateSql = "UPDATE dealer SET password='$password', contactNumber='$contactNumber', faxNumber='$faxNumber', deliveryAddress='$deliveryAddress' WHERE dealerID='$dealerID'";

    if (mysqli_query($conn, $updateSql)) {
        echo "<script>alert('Information updated successfully'); window.location.href='update_info.php';</script>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Dealer Information</title>
<link rel="stylesheet" type="text/css" href="defcss.css">
</head>
<body>

<div class="sidebar">
  <nav>
    <div class="logo">SMLC Order System</div>
    <ul class="navbar">
      <li><a href="Dealerindex.html" class="active">Home</a></li>
      <li><a href="category_dealer.php">Category</a></li>
      <li><a href="create_order.php">Create Order</a></li>
      <li><a href="view_order.php">Order List</a></li>
      <li><a href="update_info.php">Updateinfo</a></li>
      <li><a href="loginfrm.php">Logout</a></li>
    </ul>
  </nav>
</div>

<div class="content">
  <h1>Update Dealer Information</h1>

  <form action="#" method="post">
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" value="<?php echo $dealer['password']; ?>" required><br><br>
    <label for="contactNumber">Contact Number:</label><br>
    <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo $dealer['contactNumber']; ?>" required><br><br>
    <label for="faxNumber">Fax Number:</label><br>
    <input type="tel" id="faxNumber" name="faxNumber" value="<?php echo $dealer['faxNumber']; ?>"><br><br>
    <label for="deliveryAddress">Delivery Address:</label><br>
    <textarea id="deliveryAddress" name="deliveryAddress" required><?php echo $dealer['deliveryAddress']; ?></textarea><br><br>
    <button type="submit" name="update">Update Information</button>
  </form>
</div>

</body>
</html>
