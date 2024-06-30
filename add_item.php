<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Item - SMLC Order System</title>
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
    <h3>Add New Item</h3>

    <form method="post" action="" enctype="multipart/form-data">
      <label for="name">Product Name:</label>
      <input type="text" id="name" name="name" required><br>

      <label for="category">Category:</label>
      <select id="category" name="category" required>
        <option value="1">A-Sheet Metal</option>
        <option value="2">B-Major Assemblies</option>
        <option value="3">C-Light Components</option>
        <option value="4">D-Accessories</option>
      </select><br>

      <label for="image">Product Image:</label>
      <input type="file" id="image" name="image" required><br>

      <label for="description">Description:</label>
      <textarea id="description" name="description" required></textarea><br>

      <label for="weight">Weight:</label>
      <input type="number" id="weight" name="weight" step="0.01" required><br>

      <label for="quantity">Stock Quantity:</label>
      <input type="number" id="quantity" name="quantity" required><br>

      <label for="price">Price:</label>
      <input type="number" id="price" name="price" step="0.01" required><br>

      <input type="submit" name="submit" value="Add Item">
    </form>

    <?php
    include 'conn.php';

    if (isset($_POST['submit'])) {
      $name = $_POST['name'];
      $category = $_POST['category'];
      $description = $_POST['description'];
      $weight = $_POST['weight'];
      $quantity = $_POST['quantity'];
      $price = $_POST['price'];

      $image = $_FILES['image']['name'];
      $targetDir = "uploads/";
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
      }
      $target = $targetDir . basename($image);

      if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "The file " . htmlspecialchars($image) . " has been uploaded.";
      } else {
        echo "Sorry, there was an error uploading your file.";
      }

      $sql = "INSERT INTO item (sparePartName, sparePartCategory, sparePartImage, sparePareDescription, weight, stockItemQty, price) VALUES ('$name', '$category', '$image', '$description', '$weight', '$quantity', '$price')";

      if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Item added successfully'); window.location.href='category.php';</script>";
      } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
      }

      mysqli_close($conn);
    }
    ?>
  </div>
</body>

</html>