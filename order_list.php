<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['salesManagerID'])) {
    echo "<script>alert('Please log in as a sales manager to view order records.'); window.location.href='loginfrm.php';</script>";
    exit();
}

$salesManagerID = $_SESSION['salesManagerID'];

$sql_orders = "SELECT orderID, dealerID, orderDateTime, deliveryDate, totalAmount, orderStatus 
               FROM orders";
$result_orders = mysqli_query($conn, $sql_orders);

if (!$result_orders) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List - SMLC Order System</title>
    <link rel="stylesheet" type="text/css" href="defcss.css">
    <style>
        .order-table, .order-table th, .order-table td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <nav>
        <div class="logo">SMLC Order System</div>
        <ul class="navbar">
            <li><a href="Managerindex.html" class="active">Home</a></li>
            <li><a href="category.php">Category</a></li>
            <li><a href="order_list.php">Order List</a></li>
            <li><a href="loginfrm.php">Logout</a></li>
        </ul>
    </nav>
</div>

<div class="content">
    <h3>Order List</h3>

    <?php
    if (mysqli_num_rows($result_orders) > 0) {
        echo "<table class='order-table'>
                <tr>
                    <th>Order ID</th>
                    <th>Dealer ID</th>
                    <th>Order Date & Time</th>
                    <th>Delivery Date</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($result_orders)) {
            echo "<tr>
                    <td><a href='order_details.php?orderID={$row['orderID']}'>{$row['orderID']}</a></td>
                    <td>{$row['dealerID']}</td>
                    <td>{$row['orderDateTime']}</td>
                    <td>{$row['deliveryDate']}</td>
                    <td>{$row['totalAmount']}</td>
                    <td>{$row['orderStatus']}</td>
                    <td><button type='button' onclick='confirmDelete({$row['orderID']}, \"{$row['deliveryDate']}\")'>Delete</button></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found.</p>";
    }

    mysqli_close($conn);
    ?>

    <script>
        function confirmDelete(orderID, deliveryDate) {
            const today = new Date();
            const delivery = new Date(deliveryDate);
            const diffTime = delivery - today;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);

            if (diffDays < 2) {
                alert("Order can only be deleted at least two days before the delivery date.");
                return;
            }

            const confirmation = confirm("Are you sure you want to delete this order?");
            if (confirmation) {
                window.location.href = `delete_order.php?orderID=${orderID}`;
            }
        }
    </script>
</div>

</body>
</html>
