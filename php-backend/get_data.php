<?php

include "conn.php";
header('Content-Type: text/plain');
header("Access-Control-Allow-Origin: *");

// get URI parameters
$type = explode('?', $_SERVER['REQUEST_URI'], 2)[1];

//select files based on it's type

$sql = "SELECT * FROM files WHERE mime_type = '{$type}'"; 

if ($conn->query($sql) === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$res = $conn->query($sql);

$result = [];
if ($res->num_rows > 0) {
    // output data of each row
    while($row = $res->fetch_assoc()) {
      array_push($result,$row);
     }
         
    echo json_encode($result);
  } else {
    echo json_encode($result);
  }
?>