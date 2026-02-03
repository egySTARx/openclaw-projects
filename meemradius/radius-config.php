<?php
/**
 * RADIUS Admin Panel - Configuration File
 * =====================================================
 * Mageek's Complete RADIUS System
 * Date: 2026-02-02
 * =====================================================
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'radius');
define('DB_USER', 'radiususer');
define('DB_PASSWORD', 'your_secure_password_here');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'RADIUS Admin Panel');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://192.168.1.50');

// Timezone
date_default_timezone_set('Africa/Cairo');

// Language
define('LANG', 'ar'); // 'ar' for Arabic, 'en' for English

// Security Settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 3600); // 1 hour in seconds
define('SESSION_TIMEOUT', 86400); // 24 hours in seconds

// Subscription Settings
define('DAILY_DURATION', 86400); // 24 hours
define('MONTHLY_DURATION', 2592000); // 30 days
define('YEARLY_DURATION', 31536000); // 365 days
define('LIFETIME_DURATION', 0); // Lifetime

// Notifications
define('NOTIFICATION_ENABLED', true);
define('EMAIL_ENABLED', false); // Enable email notifications if needed

// File Upload Settings
define('MAX_FILE_SIZE', 2097152); // 2MB
define('UPLOAD_DIR', 'uploads/');

// Session Settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Turn off display errors in production
ini_set('log_errors', 1);
ini_set('error_log', 'logs/error.log');

/**
 * Create Database Connection
 */
function getDBConnection() {
    try {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $conn = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        return $conn;
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Check if user has admin role
 */
function isAdmin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'moderator', 'superadmin']);
}

/**
 * Check if user has specific permission
 */
function hasPermission($permission) {
    if (!isAdmin()) return false;

    $permissions = $_SESSION['permissions'] ?? [];
    return isset($permissions[$permission]) && $permissions[$permission] === true;
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Get current date in Arabic
 */
function arabicDate($format = 'Y-m-d H:i:s') {
    $arabicMonths = [
        'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
        'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];

    $date = date($format);
    $parts = explode('-', $date);
    $year = $parts[0];
    $month = $arabicMonths[(int)$parts[1] - 1];
    $day = $parts[2];

    // Replace with Arabic format
    $arabicDate = "$day $month $year";

    return $arabicDate;
}

/**
 * Format time duration
 */
function formatDuration($seconds) {
    if ($seconds === 0) return 'للأبد';

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    if ($hours > 0) {
        return sprintf('%d ساعة %d دقيقة', $hours, $minutes);
    } elseif ($minutes > 0) {
        return sprintf('%d دقيقة', $minutes);
    } else {
        return sprintf('%d ثانية', $seconds);
    }
}

/**
 * Check if subscription is expired
 */
function isSubscriptionExpired($subscription_end_date) {
    if (!$subscription_end_date) return true;
    return strtotime($subscription_end_date) < time();
}

/**
 * Generate unique ID
 */
function generateID() {
    return uniqid() . '_' . bin2hex(random_bytes(16));
}

/**
 * Log activity
 */
function logActivity($user_id, $username, $activity, $details = '') {
    try {
        $conn = getDBConnection();

        $sql = "INSERT INTO access_logs (user_id, username, ip_address, mac_address, service, status, request_time)
                VALUES (:user_id, :username, :ip_address, :mac_address, :service, :status, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'username' => $username,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'mac_address' => 'N/A',
            'service' => 'admin_panel',
            'status' => 'success'
        ]);
    } catch (Exception $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

/**
 * Send notification
 */
function sendNotification($user_id, $username, $type, $message) {
    if (!NOTIFICATION_ENABLED) return;

    try {
        $conn = getDBConnection();

        $sql = "INSERT INTO notifications (user_id, username, notification_type, message)
                VALUES (:user_id, :username, :type, :message)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'username' => $username,
            'type' => $type,
            'message' => $message
        ]);
    } catch (Exception $e) {
        error_log("Notification Error: " . $e->getMessage());
    }
}

/**
 * Get user statistics
 */
function getUserStatistics() {
    try {
        $conn = getDBConnection();

        // Total users
        $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE subscription_status = 'active'");
        $total_users = $stmt->fetch()['total'];

        // New users this month
        $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $new_users = $stmt->fetch()['total'];

        // Subscription expiration (7 days)
        $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE subscription_status = 'active' AND subscription_end_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)");
        $expiring_soon = $stmt->fetch()['total'];

        // Failed logins
        $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE failed_login_attempts > 0");
        $blocked_users = $stmt->fetch()['total'];

        return [
            'total_users' => $total_users,
            'new_users' => $new_users,
            'expiring_soon' => $expiring_soon,
            'blocked_users' => $blocked_users
        ];
    } catch (Exception $e) {
        return [
            'total_users' => 0,
            'new_users' => 0,
            'expiring_soon' => 0,
            'blocked_users' => 0
        ];
    }
}

/**
 * Get recent activity
 */
function getRecentActivity($limit = 10) {
    try {
        $conn = getDBConnection();

        $sql = "SELECT * FROM access_logs ORDER BY request_time DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
