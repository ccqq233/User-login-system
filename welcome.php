<?php
session_start();
include 'db_config.php';

if (isset($_SESSION['session_id'])) {
  $session_id = mysql_real_escape_string($_SESSION['session_id']);
  $check_expire_query = "SELECT expire_date FROM sessions WHERE session_id = '$session_id'";
  $expire_result = mysql_query($check_expire_query, $conn);
  if ($expire_result) {
      $expire_row = mysql_fetch_assoc($expire_result);
      $expire_date = strtotime($expire_row['expire_date']);
      $current_time = time();
      if ($expire_date < $current_time) {
          //Display interface for session expiration requiring re login
          session_destroy();
          echo "Session expired. Please <a href='login.php'>login</a> again.";
          exit;
      }
  } else {
      die("Error checking session expire date: " . mysql_error());
  }
} else {
  header("Location: login.php");
  exit;
}

$session_id = $_SESSION['session_id'];

//The process of handling concurrent login
if (isset($_GET["action"])) {
    $action = $_GET["action"];
    if ($action == "deny") {
        if (isset($_GET["other_session"])) {
            $other_session = $_GET["other_session"];
            $query = "DELETE FROM sessions WHERE session_id = '$other_session'";
            if (!mysql_query($query, $conn)) {
                echo "Error deleting session: " . mysql_error();
            }
        } else {
            echo "Missing session parameter for deny action.";
        }
    } elseif ($action == "allow") {
      
    }
}

//Welcome Page
$query = "SELECT s.username, u.visit_count FROM sessions s INNER JOIN users u ON s.username = u.username WHERE s.session_id = '$session_id'";
$result = mysql_query($query, $conn);
if (!$result) {
    die("Query error: " . mysql_error());
}

if (mysql_num_rows($result) == 0) {
    echo "Session expired. Please <a href='login.php'>login</a> again.";
    exit;
}

$row = mysql_fetch_assoc($result);
$username = $row['username'];
$visit_count = $row['visit_count'];

//Update access count (only updated when it is not a concurrent operation and the session has not expired)
if (!isset($_GET["action"])) {
    $visit_count++;
    $update_query = "UPDATE users SET visit_count = $visit_count WHERE username = '$username'";
    if (!mysql_query($update_query, $conn)) {
        echo "Error updating visit count: " . mysql_error();
    }
}

$query = "UPDATE sessions SET last_access_date = NOW(), expire_date = DATE_ADD(NOW(), INTERVAL 1 MINUTE) WHERE session_id = '$session_id'";
if(!mysql_query($query, $conn)){
    die("Error updating session last access time: " . mysql_error());
}

echo "Welcome " . $username . "!<br>";
echo "This is your " . $visit_count . " visit.<br>";
echo "<form method='post'><input type='submit' value='Refresh'></form>";

//Concurrent login detection and display
ob_start();

$query_other_sessions = "SELECT * FROM sessions WHERE username = (SELECT username FROM sessions WHERE session_id = '$session_id') AND session_id != '$session_id'";
$result_other_sessions = mysql_query($query_other_sessions, $conn);
if (!$result_other_sessions) {
    die("Query other sessions error: " . mysql_error());
}

if (mysql_num_rows($result_other_sessions) > 0) {
    while ($row_other_sessions = mysql_fetch_assoc($result_other_sessions)) {
        $expire_date = strtotime($row_other_sessions['expire_date']);
        $current_time = time();

        if ($expire_date > $current_time) {
            
            echo "<br>User " . $row_other_sessions['username'] . " is logged in from IP address: " . $row_other_sessions['ip_address'] . ". "; // œ‘ æIPµÿ÷∑
            echo "<a href='welcome.php?action=deny&other_session=" . $row_other_sessions["session_id"] . "'>Click here to end this session</a> or ";
            echo "<a href='welcome.php?action=allow'>Allow</a>";
        } else {
            //If the session has expired, delete it from the table
            $expired_session_id = mysql_real_escape_string($row_other_sessions['session_id']);
            $delete_expired_query = "DELETE FROM sessions WHERE session_id = '$expired_session_id'";
            if (!mysql_query($delete_expired_query, $conn)) {
                echo "Error deleting expired session: " . mysql_error();
            }
        }
    }
}

$concurrent_login_output = ob_get_clean();
echo $concurrent_login_output;

mysql_close($conn);
?>