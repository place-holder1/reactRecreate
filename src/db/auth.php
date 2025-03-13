<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    // Access and sanitize text fields
    $username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : '';
    $password = isset($_POST['password']) ? filter_var($_POST['password'], FILTER_SANITIZE_STRING) : '';
    // Validate required fields for both login and register
    if (empty($username) || empty($password)) {
        echo json_encode(["error" => "Username and password are required."]);
        exit;
    }
        // Hash the password
        $hashedPassword = md5($password); //PHP version doesn't support password_hash($password, PASSWORD_BCRYPT)
    // Validate email format
    if ($action === 'register') {
        if (empty($email)) {
            echo json_encode(["error" => "Email is required for registration."]);
            exit;
        }

        if (!$email) {
            echo json_encode(["error" => "Invalid email format."]);
            exit;
        }


        try {
            $stmt = $conn->prepare("INSERT INTO profile_app_users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $email);

            if ($stmt->execute()) {
                // Retrieve the user ID of the newly created user
                $user_id = $conn->insert_id;

                // Store user information in the session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;

                echo json_encode(["success" => "$username registered successfully."]);
                exit;
            } else {
                echo json_encode(["error" => "Error registering user: " . $stmt->error]);
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Error: " . $e->getMessage()); // Log the error message
            if ($e->getCode() == 23000) { // Duplicate entry error
                echo json_encode(["error" => "Username or email already exists."]);
            } else {
                echo json_encode(["error" => "Registration failed."]);
            }
            exit;
        }
    } elseif ($action === 'login') {
        try {
            // Check if the user exists in the database
            $stmt = $conn->prepare("SELECT id, username, password FROM profile_app_users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($id, $username, $hashedPassword);
            $stmt->fetch();

            if ($username) {
                // Verify the entered password with the hashed password in the database
                if (md5($password) == $hashedPassword) {    //PHP version doesn't support password_verify($password, $user['password']) which works with password_hash($password, PASSWORD_BCRYPT)
                    // Store user ID in session for authentication
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;

                    echo json_encode(["success" => "$username logged in successfully."]);
                    exit;
                } else {
                    echo json_encode(["error" => "Invalid password."]);
                    exit;
                }
            } else {
                echo json_encode(["error" => "User not found."]);
                exit;
            }
        } catch (mysqli_sql_exception $e) { // This requires PHP 5.5.0 or later
            error_log("Error: " . $e->getMessage()); // Log the error message
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(["error" => "Invalid action."]);
        exit;
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
    exit;
}
?>
