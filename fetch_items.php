<?php
include 'conn.php';

$category = isset($_POST['category']) ? $_POST['category'] : '';

$sql_items = "SELECT * FROM item WHERE stockItemQty > 0" . ($category ? " AND sparePartCategory='$category'" : "") . " ORDER BY sparePartName";
$result_items = mysqli_query($conn, $sql_items);

$items = [];
while ($row = mysqli_fetch_assoc($result_items)) {
    $items[] = $row;
}

echo json_encode($items);
?>
