<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['dealerID'])) {
    echo "<script>alert('Please log in as a dealer to delete an order.'); window.location.href='loginfrm.php';</script>";
    exit();
}

if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];
    $dealerID = $_SESSION['dealerID'];

    $sql_check = "SELECT * FROM orders WHERE orderID = '$orderID' AND dealerID = '$dealerID'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {

        $row = mysqli_fetch_assoc($result_check);
        $deliveryDate = $row['deliveryDate'];
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

            echo "<script>alert('Order deleted successfully'); window.location.href='view_order.php';</script>";
        } else {
            echo "<script>alert('Order can only be deleted at least two days before the delivery date.'); window.location.href='view_orders.php';</script>";
        }
    } else {
        echo "<script>alert('Order not found or does not belong to you.'); window.location.href='view_order.php';</script>";
    }
} else {
    echo "<script>alert('No order ID provided.'); window.location.href='view_order.php';</script>";
}
?>
