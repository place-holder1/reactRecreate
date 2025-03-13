<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';

$upload_dir = 'uploads/';
$allowed_types = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'); // Allowed file types
$max_size = 2 * 1024 * 1024; // Maximum file size (2MB)

// Retrieve profile ID (if updating)
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

// File upload logic (if a new file is provided)
$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $file_type = $file['type'];
    $file_size = $file['size'];

    // Validate file type
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.'));
        exit;
    }

    // Validate file size
    if ($file_size > $max_size) {
        echo json_encode(array('success' => false, 'message' => 'File size is too large. Maximum size is 2MB.'));
        exit;
    }

    // Generate a unique filename to avoid overwriting
    $unique_name = uniqid() . '_' . basename($file['name']);
    $upload_path = $upload_dir . $unique_name;

    // Move the file to the upload directory
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $image_url = 'https://' . $_SERVER['HTTP_HOST'] . 'elijah-gill' . $upload_path;
    } else {
        echo json_encode(array('success' => false, 'message' => 'Error uploading the file.'));
        exit;
    }
}

// Access and sanitize text fields
$name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : '';
$title = isset($_POST['title']) ? filter_var($_POST['title'], FILTER_SANITIZE_STRING) : '';
$bio = isset($_POST['bio']) ? filter_var($_POST['bio'], FILTER_SANITIZE_STRING) : '';

// Validate email format
if (!$email) {
    echo json_encode(array('success' => false, 'message' => 'Invalid email format'));
    exit;
}

// **Check if we are updating or creating a new profile**
if ($id) {
    // **UPDATE EXISTING PROFILE**
    $query = "UPDATE user_profiles SET name = ?, email = ?, title = ?, bio = ?";
    $params = array($name, $email, $title, $bio);
    $types = "ssss";

    // Only update the image if a new one is uploaded
    if ($image_url) {
        $query .= ", image_url = ?";
        $params[] = $image_url;
        $types .= "s";
    }

    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    if ($image_url) {
        $stmt->bind_param($types, $name, $email, $title, $bio, $image_url, $id);
    } else {
        $stmt->bind_param($types, $name, $email, $title, $bio, $id);
    }

    $action = "updated";
} else {
    // **CREATE NEW PROFILE**
    if (!$image_url) {
        echo json_encode(array('success' => false, 'message' => 'Profile image is required for new profiles.'));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO user_profiles (name, email, title, bio, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $title, $bio, $image_url);
    $action = "created";
}

// Execute query and check if successful
if ($stmt->execute()) {
    echo json_encode(array(
        'success' => true,
        'id' => $id ? $id : $conn->insert_id,
        'url' => $image_url,
        'message' => "Profile $action successfully."
    ));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error saving profile data.'));
}

$stmt->close();
$conn->close();
