<?php
$db_host = 'localhost';
$db_user = 'root'; 
$db_pass = 'root'; 
$db_name = 'user_system'; 

$conn = mysql_connect($db_host, $db_user, $db_pass);
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db($db_name, $conn);
?>