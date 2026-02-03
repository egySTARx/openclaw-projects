<?php
/**
 * MEEMRADIUS - Database Configuration
 * Pre-configured with security settings
 */

// Database Connection
define('DB_HOST', 'localhost');
define('DB_NAME', 'meemradius');
define('DB_USER', 'meemradius_admin');
define('DB_PASS', 'MeemRadius123!'); // 🔐 Secure password

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Session settings
define('SESSION_NAME', 'meemradius_session');
define('SESSION_TIMEOUT', 7200); // 2 hours

// Timezone
date_default_timezone_set('Asia/Cairo');

// System configuration
define('APP_NAME', 'MeemRadius');
define('APP_VERSION', '1.0.0');
define('BASE_URL', '/meemradius');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('LOG_DIR', __DIR__ . '/logs/');

// FreeRADIUS Configuration
define('RADIUS_SERVER', '127.0.0.1');
define('RADIUS_SECRET', 'MeemSecret456!'); // 🔐 Secure secret

// Security
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_TIMEOUT_SECONDS', 7200);
define('ENCRYPTION_KEY', 'MeemRadius@123'); // 🔐 Secure key

// Initialize session
session_name(SESSION_NAME);
session_start();
?>
