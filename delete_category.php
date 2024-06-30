<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category - SMLC Order System</title>
    <link rel="stylesheet" type="text/css" href="defcss.css">
</head>

<body>

    <div class="sidebar">
        <nav>
            <div class="logo">SMLC Order System</div>
            <ul class="navbar">
                <li><a href="Managerindex.html">Home</a></li>
                <li><a href="category.php">Category</a></li>
                <li><a href="add_item.php">Add New Item</a></li>
                <li><a href="delete_category.php">Delete Category</a></li>
                <li><a href="SalesManagerSide/CreateOrder.html">Create Order</a></li>
                <li><a href="SalesManagerSide/OrderList.html">Order List</a></li>
            </ul>
            <ul class="navbar">
                <li><a href="SalesManagerSide/Makeuporder.html">Check Out</a></li>
                <li><a href="loginfrm.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="content">
        <h3>Delete Category</h3>

        <form method="post" action="">
            <label for="category">Select Category to Delete:</label>
            <select name="category" id="category" required>
                <?php
                include 'conn.php';

                $sql = "SELECT * FROM item"; 
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories found</option>";
                }

                mysqli_close($conn);
                ?>
            </select>
            <input type="submit" name="delete" value="Delete Category">
        </form>

        <?php
        if (isset($_POST['delete'])) {
            include 'conn.php';
            $categoryId = $_POST['category'];

            $sql = "DELETE FROM item WHERE id = '$sparPartCategoryId'";

            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Category deleted successfully');</script>";
            } else {
                echo "<script>alert('Error deleting category');</script>";
            }

            mysqli_close($conn);

            echo "<script>window.location.href='delete_category.php';</script>";
        }
        ?>
    </div>
</body>

</html>
