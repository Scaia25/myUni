<?php 
$db_hostname = "127.0.0.1";
$db_user = "root";
$db_password = "";
$db_name = "myuni";

$conn = new mysqli($db_hostname, $db_user, $db_password, $db_name);

$conn->set_charset("utf8mb4");
?>