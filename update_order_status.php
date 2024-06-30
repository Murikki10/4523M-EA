<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['salesManagerID'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in as sales manager']);
    exit();
}

$salesManagerID = $_SESSION['salesManagerID'];
$data = json_decode(file_get_contents('php://input'), true);

$orderID = $data['orderID'];
$status = $data['status'];

$sql_update_order = "UPDATE orders SET salesManagerID = '$salesManagerID', orderStatus = '$status' WHERE orderID = '$orderID'";
if (mysqli_query($conn, $sql_update_order)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating order: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
