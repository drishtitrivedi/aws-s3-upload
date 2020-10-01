<?php

include "conn.php";
header('Content-Type: text/plain');

header("Access-Control-Allow-Origin: *");

// get user data from uri parameters
$uri_params = explode('?', $_SERVER['REQUEST_URI'], 2)[1];
$auth_data = explode("/",$uri_params);
$username = $auth_data[0];
$password = $auth_data[1];

$sql = "SELECT * FROM users WHERE `username` = '{$username}' and `password` = '{$password}'";
if ($conn->query($sql) === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$res = $conn->query($sql);

$result = [];
if ($res->num_rows > 0) {
   
    while($row = $res->fetch_assoc()) {
     array_push($result,$row);
    }        
        echo json_encode($result);
  } else {
    echo "0 results";
  }
?>