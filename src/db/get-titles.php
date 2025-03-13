<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Prepare the query to get unique titles
$sql = "SELECT DISTINCT title FROM user_profiles ORDER BY title ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL Prepare Failed: " . $conn->error]));
}

$stmt->execute();
$stmt->bind_result($title);

$titles = [];
while ($stmt->fetch()) {
    if (!empty($title)) { 
        $titles[] = $title;
    }
}

$stmt->close();
$conn->close();

// Return JSON response
echo json_encode(["titles" => $titles]);
?>
