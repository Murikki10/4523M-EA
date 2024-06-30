<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login label, .login input {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        .login input[type="submit"] {
            background-color: #5cb85c;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        .login input[type="submit"]:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <form method="post" action="" class="login">
        <h1>LMCS Login Form</h1>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" name="submit" value="Login">
    </form>

    <?php
    include 'conn.php';

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt_dealer = $conn->prepare("SELECT * FROM dealer WHERE dealerID = ? AND password = ?");
        $stmt_dealer->bind_param("ss", $username, $password); 
        $stmt_dealer->execute();
        $result_dealer = $stmt_dealer->get_result();

        $stmt_salesmanager = $conn->prepare("SELECT * FROM salesmanager WHERE salesManagerID = ? AND password = ?");
        $stmt_salesmanager->bind_param("ss", $username, $password); 
        $stmt_salesmanager->execute();
        $result_salesmanager = $stmt_salesmanager->get_result();

        if ($result_dealer->num_rows > 0) {
            $_SESSION['dealerID'] = $username;
            echo '<script>alert("Login successful"); window.location.href = "Dealerindex.html";</script>';
        } else if ($result_salesmanager->num_rows > 0) {
            $_SESSION['salesManagerID'] = $username;
            echo '<script>alert("Login successful"); window.location.href = "Managerindex.html";</script>';
        } else {
            echo '<script>alert("Login failed");</script>';
        }

        $stmt_dealer->close();
        $stmt_salesmanager->close();
        $conn->close();
    }
    ?>
</body>
</html>
