<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Get pagination parameters
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;
$maxLimit = 50; // Maximum limit to prevent excessive data load

// Ensure page is at least 1
if ($page < 1) {
    $page = 1;
}

// Restrict limit to the max allowed value
if ($limit > $maxLimit) {
    $limit = $maxLimit;
}

$offset = ($page - 1) * $limit;

// Get filtering parameter
$filterName = isset($_GET["name"]) ? trim($_GET["name"]) : "";
$filterTitle = isset($_GET["title"]) ? trim($_GET["title"]) : "";

// Base SQL query
$sql = "SELECT id, name, email, title, bio, image_url FROM user_profiles WHERE 1=1";
$types = "";
$params = [];

// Name search (LIKE for partial match)
if (!empty($filterName)) {
    $sql .= " AND name LIKE ?";
    $params[] = "%" . $filterName . "%";
    $types .= "s";
}

// Title search (Exact match for dropdown selection)
if (!empty($filterTitle)) {
    $sql .= " AND title = ?";
    $params[] = $filterTitle;
    $types .= "s";
}

// Pagination
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL Prepare Failed: " . $conn->error]));
}
// Manually bind parameters
if (!empty($params)) {
    if (count($params) == 1) {
        $stmt->bind_param($types, $params[0]);
    } elseif (count($params) == 2) {
        $stmt->bind_param($types, $params[0], $params[1]);
    } elseif (count($params) == 3) {
        $stmt->bind_param($types, $params[0], $params[1], $params[2]);
    } elseif (count($params) == 4) {
        $stmt->bind_param($types, $params[0], $params[1], $params[2], $params[3]);
    }
}

$stmt->execute();
$profiles = [];
// Bind the result variables
$stmt->bind_result($id, $name, $email, $title, $bio, $image_url);
// Fetch the result
while ($stmt->fetch()) {
    $profiles[] = [
        "id" => $id,
        "name" => $name,
        "email" => $email,
        "title" => $title,
        "bio" => $bio,
        "image_url" => $image_url
    ];

}
// Get total record count for pagination
$countSql = "SELECT COUNT(*) FROM user_profiles WHERE 1=1";
$countTypes = "";
$countParams = [];

if (!empty($filterName)) {
    $countSql .= " AND name LIKE ?";
    $countParams[] = "%" . $filterName . "%";
    $countTypes .= "s";
}
if (!empty($filterTitle)) {
    $countSql .= " AND title = ?";
    $countParams[] = $filterTitle;
    $countTypes .= "s";
}

$countStmt = $conn->prepare($countSql);
if (!$countStmt) {
    die(json_encode(["error" => "SQL Prepare Failed: " . $conn->error]));
}
// Manually bind count query parameters
if (!empty($countParams)) {
    if (count($countParams) == 1) {
        $countStmt->bind_param($countTypes, $countParams[0]);
    } elseif (count($countParams) == 2) {
        $countStmt->bind_param($countTypes, $countParams[0], $countParams[1]);
    }
}

$countStmt->execute();
$countStmt->bind_result($totalRecords);
$countStmt->fetch();
$countStmt->close();

// Return JSON response
echo json_encode([
    "page" => $page,
    "limit" => $limit,
    "count" => $totalRecords,
    "profiles" => $profiles
]);

// Close connections
$stmt->close();
$conn->close();
?>
