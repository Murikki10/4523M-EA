<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['salesManagerID'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in as a sales manager to update order status.']);
    exit();
}

$salesManagerID = $_SESSION['salesManagerID'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['orderID']) || !isset($data['status']) || !isset($data['salesManagerID']) || !isset($data['salesManagerName']) || !isset($data['salesManagerContact'])) {
        echo json_encode(['success' => false, 'message' => 'Order ID, status, sales manager ID, name and contact are required.']);
        exit();
    }

    $orderID = $data['orderID'];
    $status = $data['status'];
    $salesManagerID = $data['salesManagerID'];
    $salesManagerName = $data['salesManagerName'];
    $salesManagerContact = $data['salesManagerContact'];

    $sql_update = "UPDATE orders SET orderStatus = '$status', salesManagerID = '$salesManagerID' WHERE orderID = '$orderID'";
    if (mysqli_query($conn, $sql_update)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating order status.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
