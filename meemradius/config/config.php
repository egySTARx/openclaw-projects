<?php
/**
 * ============================================
 * RADIUS ADMIN PANEL - CONFIGURATION
 * تعليمات الإعدادات (Arabic Comments)
 * ============================================
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'radius_admin');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'RADIUS Admin Panel');
define('SITE_DESCRIPTION', 'لوحة إدارة RADIUS الكاملة');
define('BASE_URL', 'http://localhost/radius-admin');
define('BASE_PATH', __DIR__);

// RADIUS Server Configuration
define('RADIUS_SERVER', '127.0.0.1');
define('RADIUS_PORT', 1812);
define('RADIUS_SECRET', 'radius_secret');
define('RADIUS_PROTOCOL', 'udp');

// Timezone
date_default_timezone_set('UTC');

// Language
define('LANG', 'ar');

// Pagination
define('PER_PAGE', 20);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 2097152); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'txt']);

// Session Settings
define('SESSION_TIMEOUT', 7200); // 2 hours
define('LOGIN_REMEMBER_ME', true);

// Password Hashing
define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

// Logging
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE', BASE_PATH . '/logs/app.log');

// Notification Settings
define('EMAIL_FROM', 'noreply@radius.local');
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 25);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Encryption
define('ENCRYPTION_KEY', 'your-32-character-encryption-key-here');
define('ENCRYPTION_METHOD', 'AES-256-CBC');

// Session Cookie
define('SESSION_COOKIE_NAME', 'radius_admin_session');
define('SESSION_COOKIE_PATH', '/');
define('SESSION_COOKIE_DOMAIN', '');
define('SESSION_COOKIE_SECURE', false);
define('SESSION_COOKIE_HTTPONLY', true);

// Enable Debug Mode
define('DEBUG_MODE', false);

// API Keys (for external integrations)
define('SHELLY_API_KEY', '');
define('ZIGBEE2MQTT_API_KEY', '');

// Default Password (For initial setup only)
define('DEFAULT_PASSWORD', 'admin123');

// Paths
define('ASSETS_PATH', BASE_PATH . '/assets');
define('VIEWS_PATH', BASE_PATH . '/views');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGS_PATH', BASE_PATH . '/logs');

// Database Connection Function
function getDBConnection() {
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed. Please contact administrator.');
            }
        }
    }

    return $conn;
}

// Redirect Function
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

// Check Auth Function
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Require Auth Function
function requireAuth() {
    if (!isLoggedIn()) {
        redirect('/login.php');
        exit;
    }
}

// Get Current Admin Function
function getCurrentAdmin() {
    if (!isLoggedIn()) {
        return null;
    }

    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

// Hash Password Function
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify Password Function
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate Random Token Function
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Sanitize Input Function
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Format Date Function
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

// Log Function
function logMessage($message, $level = 'INFO') {
    $logMessage = sprintf(
        "[%s] [%s] %s - %s\n",
        formatDate(date('Y-m-d H:i:s')),
        strtoupper($level),
        $_SESSION['admin_id'] ?? 'system',
        $message
    );

    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
}

// Check Subscription Expiry Function
function checkSubscriptionExpiry($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT s.end_date, u.email
        FROM subscriptions s
        INNER JOIN users u ON s.user_id = u.id
        WHERE s.user_id = ? AND s.status = 'active'
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

// Send Notification Function
function sendNotification($userId, $type, $message) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, type, message, is_read)
        VALUES (?, ?, ?, 'no')
    ");
    return $stmt->execute([$userId, $type, $message]);
}

// Get unread notifications count
function getUnreadNotifications() {
    if (!isLoggedIn()) return 0;

    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count
        FROM notifications
        WHERE is_read = 'no'
    ");
    $stmt->execute();
    return $stmt->fetch()['count'];
}

// Start Session
session_name(SESSION_COOKIE_NAME);
session_start([
    'cookie_path' => SESSION_COOKIE_PATH,
    'cookie_domain' => SESSION_COOKIE_DOMAIN,
    'cookie_secure' => SESSION_COOKIE_SECURE,
    'cookie_httponly' => SESSION_COOKIE_HTTPONLY,
    'use_strict_mode' => true,
    'cookie_samesite' => 'Lax'
]);
