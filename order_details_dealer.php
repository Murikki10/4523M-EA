<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['dealerID'])) {
    echo "<script>alert('Please log in as a dealer to view order details.'); window.location.href='loginfrm.php';</script>";
    exit();
}

$dealerID = $_SESSION['dealerID'];
$orderID = $_GET['orderID'];

$sql_order = "SELECT o.orderID, o.salesManagerID, s.contactName AS salesManagerContactName, s.contactNumber AS salesManagerContactNumber, o.orderDateTime, o.deliveryAddress, o.deliveryDate, o.orderStatus, o.totalAmount
              FROM orders o
              LEFT JOIN salesmanager s ON o.salesManagerID = s.salesManagerID
              WHERE o.orderID = '$orderID' AND o.dealerID = '$dealerID'";
$result_order = mysqli_query($conn, $sql_order);

if (!$result_order) {
    die("Query failed: " . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($result_order);

$sql_order_items = "SELECT oi.sparePartNum, i.sparePartName, i.sparePartImage, oi.orderQty, oi.sparePartOrderPrice
                    FROM ordersitem oi
                    JOIN item i ON oi.sparePartNum = i.sparePartNum
                    WHERE oi.orderID = '$orderID'";
$result_order_items = mysqli_query($conn, $sql_order_items);

if (!$result_order_items) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - SMLC Order System</title>
    <link rel="stylesheet" type="text/css" href="defcss.css">
    <style>
        .order-table, .order-table th, .order-table td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
        }
        .item-image {
            max-width: 150px;
            max-height: 150px;
        }
    </style>
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
    <h3>Order Details</h3>

    <?php if ($order): ?>
        <table class='order-table'>
            <tr><th>Order ID</th><td><?= $order['orderID'] ?></td></tr>
            <tr><th>Sales Manager ID</th><td><?= $order['salesManagerID'] ?></td></tr>
            <tr><th>Sales Manager Contact Name</th><td><?= $order['salesManagerContactName'] ?></td></tr>
            <tr><th>Sales Manager Contact Number</th><td><?= $order['salesManagerContactNumber'] ?></td></tr>
            <tr><th>Order Date & Time</th><td><?= $order['orderDateTime'] ?></td></tr>
            <tr><th>Delivery Address</th><td><?= $order['deliveryAddress'] ?></td></tr>
            <tr><th>Delivery Date</th><td><?= $order['deliveryDate'] ?></td></tr>
            <tr><th>Order Status</th><td><?= $order['orderStatus'] ?></td></tr>
            <tr><th>Total Order Amount</th><td><?= $order['totalAmount'] ?></td></tr>
        </table>

        <h3>Order Items</h3>
        <table class='order-table'>
            <tr>
                <th>Spare Part Image</th>
                <th>Spare Part Name</th>
                <th>Order Quantity</th>
                <th>Order Price</th>
                <th>Total</th>
            </tr>
            <?php while ($item = mysqli_fetch_assoc($result_order_items)): ?>
                <tr>
                    <td><img src='uploads/<?= $item['sparePartImage'] ?>' class='item-image' alt='Spare Part Image'></td>
                    <td><?= $item['sparePartName'] ?></td>
                    <td><?= $item['orderQty'] ?></td>
                    <td><?= $item['sparePartOrderPrice'] ?></td>
                    <td><?= $item['orderQty'] * $item['sparePartOrderPrice'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Order not found.</p>
    <?php endif; ?>
</div>

</body>
</html>
