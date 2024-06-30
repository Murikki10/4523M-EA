<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['dealerID'])) {
    echo "<script>alert('Please log in as a dealer to view order records.'); window.location.href='loginfrm.php';</script>";
    exit();
}

$dealerID = $_SESSION['dealerID'];

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'orderID';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'DESC' ? 'DESC' : 'ASC';

$sql_orders = "SELECT o.orderID, o.salesManagerID, o.orderDateTime, o.deliveryAddress, o.deliveryDate, o.orderStatus, o.totalAmount
               FROM orders o
               WHERE o.dealerID = '$dealerID'
               ORDER BY $order_by $order_dir";

$result_orders = mysqli_query($conn, $sql_orders);

if (!$result_orders) {
    die("Query failed: " . mysqli_error($conn));
}

$sort_columns = [
    'orderID' => 'Order ID',
    'orderDateTime' => 'Order Date & Time',
    'deliveryDate' => 'Delivery Date'
];

function getOppositeOrderDir($current_dir) {
    return $current_dir == 'ASC' ? 'DESC' : 'ASC';
}

if (isset($_GET['cancelOrderID'])) {
    $orderID = $_GET['cancelOrderID'];
    $sql_check_order = "SELECT * FROM orders WHERE orderID = '$orderID' AND dealerID = '$dealerID'";
    $result_check_order = mysqli_query($conn, $sql_check_order);

    if (mysqli_num_rows($result_check_order) > 0) {
        $order = mysqli_fetch_assoc($result_check_order);
        $deliveryDate = $order['deliveryDate'];
        $today = date('Y-m-d');
        $diff = (strtotime($deliveryDate) - strtotime($today)) / (60 * 60 * 24);

        if ($diff >= 2) {
            $sql_items = "SELECT * FROM ordersitem WHERE orderID = '$orderID'";
            $result_items = mysqli_query($conn, $sql_items);

            while ($item = mysqli_fetch_assoc($result_items)) {
                $sparePartNum = $item['sparePartNum'];
                $orderQty = $item['orderQty'];

                $sql_update_stock = "UPDATE item SET stockItemQty = stockItemQty + '$orderQty' WHERE sparePartNum = '$sparePartNum'";
                mysqli_query($conn, $sql_update_stock);
            }

            $sql_delete_items = "DELETE FROM ordersitem WHERE orderID = '$orderID'";
            mysqli_query($conn, $sql_delete_items);

            $sql_delete_order = "DELETE FROM orders WHERE orderID = '$orderID'";
            mysqli_query($conn, $sql_delete_order);

            echo "<script>alert('Order cancelled successfully'); window.location.href='view_order.php';</script>";
        } else {
            echo "<script>alert('Order can only be cancelled at least two days before the delivery date.'); window.location.href='view_order.php';</script>";
        }
    } else {
        echo "<script>alert('Order not found or does not belong to you.'); window.location.href='view_order.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - SMLC Order System</title>
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
    <script>
        function confirmCancel(orderID, deliveryDate) {
            const today = new Date();
            const delivery = new Date(deliveryDate);
            const diffTime = delivery - today;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);

            if (diffDays < 2) {
                alert("Order can only be cancelled at least two days before the delivery date.");
                return;
            }

            const confirmation = confirm("Are you sure you want to cancel this order?");
            if (confirmation) {
                window.location.href = `view_order.php?cancelOrderID=${orderID}`;
            }
        }
    </script>
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
    <h3>View Orders</h3>

    <form method="get" action="">
        <label for="order_by">Order By:</label>
        <select id="order_by" name="order_by">
            <?php
            foreach ($sort_columns as $col => $label) {
                $selected = $order_by == $col ? 'selected' : '';
                echo "<option value='$col' $selected>$label</option>";
            }
            ?>
        </select>
        <select id="order_dir" name="order_dir">
            <option value="ASC" <?php if ($order_dir == 'ASC') echo 'selected'; ?>>Ascending</option>
            <option value="DESC" <?php if ($order_dir == 'DESC') echo 'selected'; ?>>Descending</option>
        </select>
        <button type="submit">Sort</button>
    </form>

    <?php
    if (mysqli_num_rows($result_orders) > 0) {
        echo "<table class='order-table'>
                <tr>
                    <th>Order ID</th>
                    <th>Sales Manager ID</th>
                    <th>Order Date & Time</th>
                    <th>Delivery Address</th>
                    <th>Delivery Date</th>
                    <th>Order Status</th>
                    <th>Total Order Amount</th>
                    <th>Action</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($result_orders)) {
            echo "<tr>
                    <td><a href='order_details_dealer.php?orderID={$row['orderID']}'>{$row['orderID']}</a></td>
                    <td>{$row['salesManagerID']}</td>
                    <td>{$row['orderDateTime']}</td>
                    <td>{$row['deliveryAddress']}</td>
                    <td>{$row['deliveryDate']}</td>
                    <td>{$row['orderStatus']}</td>
                    <td>{$row['totalAmount']}</td>
                    <td><button type='button' onclick='confirmCancel({$row['orderID']}, \"{$row['deliveryDate']}\")'>Cancel</button></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found.</p>";
    }

    mysqli_close($conn);
    ?>
</div>

</body>
</html>
