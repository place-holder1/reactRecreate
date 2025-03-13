<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$upload_dir = 'uploads/';
$allowed_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']; // Allowed file types
$max_size = 2 * 1024 * 1024; // Maximum file size (2MB)

// Check if the file is an image
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or error during upload.']);
    exit;
}

$file = $_FILES['image'];
$file_type = $file['type'];
$file_size = $file['size'];

// Validate file type
if (!in_array($file_type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.']);
    exit;
}

// Validate file size
if ($file_size > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size is too large. Maximum size is 2MB.']);
    exit;
}

// Generate a unique filename to avoid overwriting
$unique_name = uniqid() . '_' . basename($file['name']);
$upload_path = $upload_dir . $unique_name;

// Move the file to the upload directory
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Return the image URL
    $image_url = 'https://' . $_SERVER['HTTP_HOST'] . '/~zong6/profile-app/' . $upload_path;
} else {
    echo json_encode(['success' => false, 'message' => 'There was an error uploading the file.']);
    exit; // Add exit to stop further execution if file upload fails
}


// Access text fields from the form submission
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$bio = isset($_POST['bio']) ? $_POST['bio'] : '';
// Sanitize inputs
$name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$bio = filter_var($_POST['bio'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check for invalid email
if (!$email) {
    echo json_encode(['success' => false, "message" => "Invalid email format"]);
    exit;
}
// Prepare and bind SQL
$stmt = $conn->prepare("INSERT INTO user_profiles (name, email, title, bio, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $title, $bio, $image_url);

// Execute query and check if successful
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'url' => $image_url, 'message' => 'Profile saved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'There was an error saving the data.']);
}

$stmt->close();
$conn->close();
