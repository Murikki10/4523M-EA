<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Category - SMLC Order System</title>
    <link rel="stylesheet" type="text/css" href="defcss.css">
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
        <h3>Product Category</h3>

        <form method="post" action="">
            <label for="category">Select Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="" disabled selected>Select a category</option>
                <option value="1">A-Sheet Metal</option>
                <option value="2">B-Major Assemblies</option>
                <option value="3">C-Light Components</option>
                <option value="4">D-Accessories</option>
            </select>
        </form>

        <?php
        include 'conn.php';

        if (isset($_POST['category']) && $_POST['category'] != '') {
            $category = $_POST['category'];

            $sql = "SELECT * FROM item WHERE sparePartCategory = '$category'";

            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                echo "<table>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Weight</th>
                        <th>Stock Quantity</th>
                    </tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td><img src='uploads/" . $row['sparePartImage'] . "' alt='Product Image'></td>
                        <td>" . $row['sparePartName'] . "</td>
                        <td>" . $row['price'] . "</td>
                        <td>" . $row['sparePareDescription'] . "</td>
                        <td>" . $row['weight'] . "</td>
                        <td>" . $row['stockItemQty'] . "</td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No products found.</p>";
            }
        }

        mysqli_close($conn);
        ?>

    </div>
</body>

</html>
