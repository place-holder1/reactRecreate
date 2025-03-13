<?php
$servername = "https://premium310.web-hosting.com:2083/cpsess9607904995/3rdparty/phpMyAdmin/index.php?route=/table/structure&db=elijtkgg_databaseTest&table=users";
$username = "elijtkgg_";
$password = "databaseTest!";
$dbname = "elijtkgg_databaseTest";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
