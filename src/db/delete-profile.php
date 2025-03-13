<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

// Check if the 'id' parameter is provided
if (isset($_GET['id'])) {
    $profileId = $_GET['id'];

    // Include the database connection
    include 'db.php';

    // SQL query to delete the profile from the database
    $stmt = $conn->prepare("DELETE FROM user_profiles WHERE id = ?");
    $stmt->bind_param("i", $profileId);  // "i" means the parameter is an integer

    if ($stmt->execute()) {
        // Profile deleted successfully
        echo json_encode(['message' => 'Profile deleted successfully']);
    } else {
        // Error deleting profile
        echo json_encode(['error' => 'Failed to delete profile']);
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Profile ID is required']);
}
?>