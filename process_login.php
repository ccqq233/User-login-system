<?php
session_start();
include 'db_config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysql_query($query, $conn);

if (mysql_num_rows($result) == 1) {
    $session_id = md5(uniqid(rand(), true)); //Generate Session ID
    $expire_time = time() + 60; //Expires in a minute
    $last_access = date('Y-m-d H:i:s');
    $expire_date = date('Y-m-d H:i:s', $expire_time);
    $ip_address = $_SERVER['REMOTE_ADDR']; 
    $query = "INSERT INTO sessions (session_id, username, expire_date, last_access_date, ip_address) VALUES ('$session_id', '$username', '$expire_date', '$last_access', '$ip_address')";
    mysql_query($query, $conn);
    $_SESSION['session_id'] = $session_id;
    header("Location: welcome.php");
} else {
    echo "Invalid username or password.";
}
mysql_close($conn);
?>