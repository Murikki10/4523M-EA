<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - SMLC Order System</title>
    <link rel="stylesheet" type="text/css" href="defcss.css">
</head>

<body>

<div class="sidebar">
        <nav>
            <div class="logo">SMLC Order System</div>
            <ul class="navbar">
                <li><a href="Managerindex.html" class="active">Home</a></li>
                <li><a href="category.php">Category</a></li>
                <li><a href="add_item.php">Add New Item</a></li>
                <li><a href="order_list.php">Order List</a></li>
                <li><a href="loginfrm.php">Logout</a></li>
            </ul>
        </nav>
    </div>

<div class="content">
    <h3>Edit Item</h3>

    <?php
    include 'conn.php';

    if (isset($_GET['id'])) {
        $itemId = $_GET['id'];
        $sql = "SELECT * FROM item WHERE sparePartNum = '$itemId'";
        $result = mysqli_query($conn, $sql);
        $item = mysqli_fetch_assoc($result);

        if (!$item) {
            echo "<p>Item not found.</p>";
            exit;
        }
    }

    if (isset($_POST['submit'])) {
        $description = $_POST['description'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];

        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $target = $targetDir . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        } else {
            $image = $item['sparePartImage'];
        }

        $updateSql = "UPDATE item SET sparePareDescription = '$description', sparePartImage = '$image', stockItemQty = '$quantity', price = '$price' WHERE sparePartNum = '$itemId'";

        if (mysqli_query($conn, $updateSql)) {
            echo "<script>alert('Item updated successfully'); window.location.href='category.php';</script>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }

    mysqli_close($conn);
    ?>

    <form method="post" action="" enctype="multipart/form-data">
        <label for="description">Spare Part Description:</label>
        <textarea id="description" name="description" required><?php echo $item['sparePareDescription']; ?></textarea><br>

        <label for="image">Spare Part Image:</label>
        <input type="file" id="image" name="image"><br>
        <img src="uploads/<?php echo $item['sparePartImage']; ?>" alt="Current Image" width="100"><br>

        <label for="quantity">Stock Item Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $item['stockItemQty']; ?>" required><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo $item['price']; ?>" step="0.01" required><br>

        <input type="submit" name="submit" value="Update Item">
    </form>
</div>
</body>
</html>
