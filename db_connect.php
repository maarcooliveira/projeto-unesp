<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "mind_map";

// $dbhost = "200.133.238.112";
// $dbuser = "maplink";
// $dbpass = "maplink2015";
// $dbname = "maplink";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
// Test if connection occurred.
if(mysqli_connect_errno()) {
die("Database connection failed: " .
     mysqli_connect_error() .
     " (" . mysqli_connect_errno() . ")"
);
}

?>
