<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session
echo json_encode(["message" => "Logged out successfully."]);
?>
