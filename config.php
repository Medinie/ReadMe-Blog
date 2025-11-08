<?php

// Function to load .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        die("Error: .env file not found at: " . $path);
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Set as constant if not already defined
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }
}

// Load environment variables from .env file
loadEnv(__DIR__ . '/.env');

// Database configuration (loaded from .env)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "<br>Please check your .env file settings.");
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Start session with custom name from .env
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// HELPER FUNCTIONS
/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in, redirect to login page if not
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

/**
 * Sanitize user input
 * @param string $data The data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Upload image file
 * @param array $file The uploaded file from $_FILES
 * @return string|false|null Filename on success, false on error, null if no file
 */
function uploadImage($file) {
    // Create uploads directory if it doesn't exist
    $upload_dir = UPLOAD_DIR;
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return false;
        }
    }
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return null;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Get allowed file types from .env
    $allowed_types = explode(',', ALLOWED_IMAGE_TYPES);
    $file_type = $file['type'];
    
    // Validate file type
    if (!in_array($file_type, $allowed_types)) {
        return false;
    }
    
    // Check file size (from .env)
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    // Generate unique filename
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $upload_dir . $new_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_filename;
    }
    
    return false;
}

/**
 * Delete image file
 * @param string $filename The filename to delete
 * @return bool True on success, false on failure
 */
function deleteImage($filename) {
    if (empty($filename)) {
        return false;
    }
    
    $filepath = UPLOAD_DIR . $filename;
    
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Get full image URL
 * @param string $filename The image filename
 * @return string Full image URL
 */
function getImageUrl($filename) {
    if (empty($filename)) {
        return '';
    }
    return UPLOAD_DIR . htmlspecialchars($filename);
}

/**
 * Format date for display
 * @param string $date The date to format
 * @return string Formatted date
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Format datetime for display
 * @param string $datetime The datetime to format
 * @return string Formatted datetime
 */
function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Redirect to a page with optional message
 * @param string $page The page to redirect to
 * @param string $message Optional success message
 */
function redirect($page, $message = '') {
    if (!empty($message)) {
        $page .= (strpos($page, '?') === false ? '?' : '&') . 'success=' . urlencode($message);
    }
    header('Location: ' . $page);
    exit();
}

/**
 * Display error message
 * @param string $message The error message
 */
function showError($message) {
    return '<div class="alert alert-error">' . htmlspecialchars($message) . '</div>';
}

/**
 * Display success message
 * @param string $message The success message
 */
function showSuccess($message) {
    return '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

/**
 * Validate password strength
 * @param string $password The password to validate
 * @return bool True if valid, false otherwise
 */
function validatePassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

/**
 * Validate email format
 * @param string $email The email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Get user by ID
 * @param int $user_id The user ID
 * @return array|null User data or null if not found
 */
function getUserById($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, email, created_at FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get total blog count for a user
 * @param int $user_id The user ID
 * @return int Total number of blogs
 */
function getUserBlogCount($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM blogpost WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

// ERROR HANDLING
// Set error reporting based on environment
// You can change this in production to hide errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// TIMEZONE SETTING
date_default_timezone_set('Asia/Colombo'); // Change to your timezone

?>