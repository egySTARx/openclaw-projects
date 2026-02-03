<?php
/**
 * MEEMRADIUS - Authentication
 */

require_once __DIR__ . '/database.php';

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Require admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        http_response_code(403);
        die('Access denied');
    }
}

/**
 * Login user
 */
function login($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }
    }

    return false;
}

/**
 * Logout user
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
?>
