<?php
    $mysql_host = 'localhost';
    $mysql_user = 'root';
    $mysql_pw = '';
    $mysql_db = 'imad';
    try {
        $conn = new mysqli($mysql_host, $mysql_user, $mysql_pw, $mysql_db);
        if ($conn->connect_error) {
            throw new Exception('Connection failed: ' . $conn->connect_error);
        }
    } catch (Exception $e) {
        die('Unable to connect to database: ' . $e->getMessage());
    }
?>