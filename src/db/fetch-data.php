<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all profiles
$result = $conn->query("SELECT id, name, email, title, bio, image_url FROM user_profiles");

// Check if any profiles exist
if ($result->num_rows > 0) {
    $profiles = [];
    while ($row = $result->fetch_assoc()) {
        $profiles[] = $row;
    }
    echo json_encode($profiles); // Return all profiles in JSON format
} else {
    echo json_encode(["error" => "No profiles found"]);
}

$conn->close();
?>
