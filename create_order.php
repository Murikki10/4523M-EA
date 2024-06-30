<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['dealerID'])) {
    echo "<script>alert('Please log in as a dealer to create an order.'); window.location.href='loginfrm.php';</script>";
    exit();
}

$sql_categories = "SELECT DISTINCT sparePartCategory FROM item";
$result_categories = mysqli_query($conn, $sql_categories);

if (isset($_POST['submit'])) {
    $dealerID = $_SESSION['dealerID'];
    $deliveryAddress = $_POST['deliveryAddress'];
    $deliveryDate = $_POST['deliveryDate'];
    $orderItems = json_decode($_POST['orderItems'], true);

    $sql_order = "INSERT INTO orders (dealerID, deliveryAddress, deliveryDate) VALUES ('$dealerID', '$deliveryAddress', '$deliveryDate')";
    if (mysqli_query($conn, $sql_order)) {
        $orderID = mysqli_insert_id($conn);
        $totalAmount = 0;

        foreach ($orderItems as $item) {
            $sparePartNum = $item['sparePartNum'];
            $orderQty = $item['orderQty'];
            $orderPrice = $item['orderPrice'];

            $sql_order_item = "INSERT INTO ordersitem (orderID, sparePartNum, orderQty, sparePartOrderPrice) VALUES ('$orderID', '$sparePartNum', '$orderQty', '$orderPrice')";
            mysqli_query($conn, $sql_order_item);

            $sql_update_stock = "UPDATE item SET stockItemQty = stockItemQty - '$orderQty' WHERE sparePartNum = '$sparePartNum'";
            mysqli_query($conn, $sql_update_stock);

            $totalAmount += $orderQty * $orderPrice;
        }

        $sql_update_order = "UPDATE orders SET totalAmount = '$totalAmount' WHERE orderID = '$orderID'";
        mysqli_query($conn, $sql_update_order);

        $orderDetails = "Order ID: $orderID\\nDelivery Address: $deliveryAddress\\nDelivery Date: $deliveryDate\\nTotal Amount: $totalAmount\\nItems:\\n";
        foreach ($orderItems as $item) {
            $orderDetails .= "{$item['sparePartName']} - Qty: {$item['orderQty']} - Price: {$item['orderPrice']}\\n";
        }

        echo "<script>alert('Order created successfully.\\n$orderDetails'); window.location.href='create_order.php';</script>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order - SMLC Order System</title>
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
        let orderItems = [];

        function addItem() {
            const sparePartNum = document.getElementById('sparePartNum').value;
            const orderQty = parseInt(document.getElementById('orderQty').value);
            const itemData = document.getElementById('sparePartNum').selectedOptions[0].dataset;
            const orderPrice = parseFloat(itemData.price);
            const itemIndex = orderItems.findIndex(item => item.sparePartNum === sparePartNum);

            if (orderQty > parseInt(itemData.stock)) {
                alert('Order quantity exceeds available stock. Please adjust the quantity.');
                return;
            }

            if (itemIndex > -1) {
                orderItems[itemIndex].orderQty += orderQty;
            } else {
                const item = {
                    sparePartNum,
                    sparePartName: itemData.name,
                    orderQty,
                    orderPrice,
                    sparePartImage: itemData.image
                };
                orderItems.push(item);
            }
            displayOrderItems();
        }

        function removeItem(sparePartNum) {
            const itemIndex = orderItems.findIndex(item => item.sparePartNum === sparePartNum);
            if (itemIndex > -1) {
                orderItems.splice(itemIndex, 1);
                displayOrderItems();
            }
        }

        function displayOrderItems() {
            let orderTable = '<table class="order-table"><tr><th>Spare Part Image</th><th>Spare Part Name</th><th>Order Quantity</th><th>Order Price</th><th>Total</th><th>Action</th></tr>';
            let totalAmount = 0;
            let totalQuantity = 0;

            if (orderItems.length > 0) {
                orderItems.forEach(item => {
                    const itemTotal = item.orderQty * item.orderPrice;
                    totalAmount += itemTotal;
                    totalQuantity += item.orderQty;
                    orderTable += `<tr>
                        <td><img src="uploads/${item.sparePartImage}" class="item-image" alt="${item.sparePartName}"></td>
                        <td>${item.sparePartName}</td>
                        <td>${item.orderQty}</td>
                        <td>${item.orderPrice.toFixed(2)}</td>
                        <td>${itemTotal.toFixed(2)}</td>
                        <td><button type="button" onclick="removeItem('${item.sparePartNum}')">Delete</button></td>
                    </tr>`;
                });

                orderTable += `<tr><td colspan="4"><strong>Total Amount</strong></td><td colspan="2"><strong>${totalAmount.toFixed(2)}</strong></td></tr>`;
                orderTable += `<tr><td colspan="4"><strong>Total Quantity</strong></td><td colspan="2"><strong>${totalQuantity}</strong></td></tr>`;
            } else {
                orderTable += `<tr><td colspan="6">No items added to the order yet.</td></tr>`;
            }

            orderTable += '</table>';

            document.getElementById('orderItemsList').innerHTML = orderTable;
            document.getElementById('orderItems').value = JSON.stringify(orderItems);
        }

        function showItemImage() {
            const itemData = document.getElementById('sparePartNum').selectedOptions[0].dataset;
            document.getElementById('selectedItemImage').src = "uploads/" + itemData.image;
        }

        function fetchItems() {
            const category = document.getElementById('category').value;
            const form = new FormData();
            form.append('category', category);

            fetch('fetch_items.php', {
                method: 'POST',
                body: form
            })
            .then(response => response.json())
            .then(data => {
                const sparePartNum = document.getElementById('sparePartNum');
                sparePartNum.innerHTML = '<option value="" disabled selected>Select a spare part</option>';

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.sparePartNum;
                    option.dataset.name = item.sparePartName;
                    option.dataset.price = item.price;
                    option.dataset.stock = item.stockItemQty;
                    option.dataset.image = item.sparePartImage;
                    option.textContent = `${item.sparePartName} (Price: ${item.price}, Stock: ${item.stockItemQty})`;
                    sparePartNum.appendChild(option);
                });

                showItemImage();
                document.getElementById('sparePartContainer').style.display = 'block';
            });
        }

        window.onload = function() {
            displayOrderItems(); 
        };
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
    <h3>Create Order</h3>

    <form method="post" action="">
        <label for="deliveryAddress">Delivery Address:</label><br>
        <textarea id="deliveryAddress" name="deliveryAddress" required></textarea><br><br>

        <label for="deliveryDate">Delivery Date:</label><br>
        <input type="date" id="deliveryDate" name="deliveryDate" required><br><br>

        <label for="category">Select Category:</label><br>
        <select id="category" name="category" onchange="fetchItems()">
            <option value="" disabled selected>Select a category</option>
            <?php
            while ($row = mysqli_fetch_assoc($result_categories)) {
                echo "<option value='" . $row['sparePartCategory'] . "'>" . $row['sparePartCategory'] . "</option>";
            }
            ?>
        </select><br><br>

        <div id="sparePartContainer" style="display:none;">
            <label for="sparePartNum">Spare Part:</label><br>
            <select id="sparePartNum" name="sparePartNum" onchange="showItemImage()"></select><br><br>

            <img id="selectedItemImage" class="item-image" alt="Selected Item Image"><br><br>

            <label for="orderQty">Order Quantity:</label><br>
            <input type="number" id="orderQty" name="orderQty" min="1"><br><br>

            <button type="button" onclick="addItem()">Add Item</button><br><br>
        </div>

        <div id="orderItemsList">
            <table class="order-table">
                <tr><th>Spare Part Image</th><th>Spare Part Name</th><th>Order Quantity</th><th>Order Price</th><th>Total</th><th>Action</th></tr>
                <tr><td colspan="6">No items added to the order yet.</td></tr>
            </table>
        </div><br>

        <input type="hidden" id="orderItems" name="orderItems">
        <button type="submit" name="submit">Create Order</button>
    </form>
</div>

</body>
</html>
