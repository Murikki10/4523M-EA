<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    $checkOrderSql = "SELECT * FROM ordersitem WHERE sparePartNum = '$itemId'";
    $orderResult = mysqli_query($conn, $checkOrderSql);

    if (mysqli_num_rows($orderResult) > 0) {
        echo "<script>alert('This item cannot be deleted as it is associated with an order.'); window.location.href='category.php';</script>";
    } else {

        $sql = "DELETE FROM item WHERE sparePartNum = '$itemId'";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Item deleted successfully'); window.location.href='category.php';</script>";
        } else {
            echo "<script>alert('Error deleting item'); window.location.href='category.php';</script>";
        }
    }
}

mysqli_close($conn);
?>
