-- =====================================================
-- Database Schema for Complete RADIUS Admin Panel
-- =====================================================
-- Mageek's Complete RADIUS System
-- Date: 2026-02-02
-- Platform: MySQL 5.7+
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE radius;

-- =====================================================
-- Table 1: Users (المستخدمين)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    subscription_type ENUM('daily', 'monthly', 'yearly', 'lifetime') DEFAULT 'monthly',
    subscription_status ENUM('active', 'expired', 'suspended', 'canceled') DEFAULT 'active',
    subscription_start_date DATETIME NOT NULL,
    subscription_end_date DATETIME,
    last_login_time DATETIME,
    failed_login_attempts INT DEFAULT 0,
    is_blocked BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_subscription_status (subscription_status),
    INDEX idx_last_login (last_login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 2: Access Logs (سجلات الوصول)
-- =====================================================
CREATE TABLE IF NOT EXISTS access_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    ip_address VARCHAR(45),
    mac_address VARCHAR(17),
    client_name VARCHAR(50),
    service VARCHAR(50),
    status ENUM('success', 'failed', 'blocked') DEFAULT 'success',
    request_time DATETIME NOT NULL,
    response_time INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_username (username),
    INDEX idx_ip_address (ip_address),
    INDEX idx_status (status),
    INDEX idx_request_time (request_time),
    INDEX idx_user_request_time (user_id, request_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 3: Networks (الشبكات)
-- =====================================================
CREATE TABLE IF NOT EXISTS networks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    network_name VARCHAR(100) NOT NULL,
    network_ip VARCHAR(45) NOT NULL, -- e.g., 192.168.1.0/24
    network_address VARCHAR(45) NOT NULL, -- e.g., 192.168.1.1
    network_secret VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    vlan_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_network_name (network_name),
    INDEX idx_network_ip (network_ip),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 4: Devices (الأجهزة)
-- =====================================================
CREATE TABLE IF NOT EXISTS devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL,
    device_type ENUM('smart_screen', 'router', 'ap', 'switch', 'shelly', 'zigbee', 'vpn', 'other') DEFAULT 'smart_screen',
    ip_address VARCHAR(45),
    mac_address VARCHAR(17),
    network_id INT,
    firmware_version VARCHAR(50),
    device_status ENUM('online', 'offline', 'maintenance', 'problem') DEFAULT 'online',
    last_seen DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE SET NULL,
    INDEX idx_device_name (device_name),
    INDEX idx_device_type (device_type),
    INDEX idx_device_status (device_status),
    INDEX idx_ip_address (ip_address),
    INDEX idx_network_id (network_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 5: Admins (المشرفين)
-- =====================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin', 'admin', 'moderator') DEFAULT 'admin',
    permissions JSON,
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 6: Notifications (الإشعارات)
-- =====================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    notification_type ENUM('subscription_expired', 'login_failed', 'new_device', 'network_change', 'alert') DEFAULT 'alert',
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_username (username),
    INDEX idx_notification_type (notification_type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 7: Settings (الإعدادات)
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('system_name', 'RADIUS Admin Panel', 'اسم النظام'),
('default_subscription_duration', '30', 'مدة الاشتراك الافتراضي بالأيام'),
('notification_enabled', 'true', 'تفعيل الإشعارات'),
('max_failed_attempts', '5', 'عدد محاولات الدخول الفاشلة قبل الحظر'),
('session_timeout', '86400', 'مدة انتهاء الجلسة بالثواني (24 ساعة)'),
('maintenance_mode', 'false', 'وضع الصيانة'),
('enable_registration', 'false', 'تفعيل التسجيل تلقائي'),
('language', 'ar', 'لغة النظام: ar/en');

-- =====================================================
-- Table 8: Device Logs (سجلات الأجهزة)
-- =====================================================
CREATE TABLE IF NOT EXISTS device_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    device_id INT,
    device_name VARCHAR(100),
    log_type ENUM('connection', 'disconnection', 'status_change', 'update', 'error') DEFAULT 'status_change',
    log_message TEXT NOT NULL,
    log_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_device_id (device_id),
    INDEX idx_log_type (log_type),
    INDEX idx_log_time (log_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 9: Subscription Renewals (تجديد الاشتراكات)
-- =====================================================
CREATE TABLE IF NOT EXISTS subscription_renewals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    old_end_date DATETIME,
    new_end_date DATETIME,
    renewal_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    renewal_type ENUM('auto', 'manual', 'extension') DEFAULT 'manual',
    INDEX idx_user_id (user_id),
    INDEX idx_renewal_date (renewal_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 10: Reports (التقارير)
-- =====================================================
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_type ENUM('daily', 'weekly', 'monthly', 'custom') DEFAULT 'daily',
    report_date DATE NOT NULL,
    active_users INT DEFAULT 0,
    total_logins INT DEFAULT 0,
    failed_logins INT DEFAULT 0,
    new_users INT DEFAULT 0,
    active_devices INT DEFAULT 0,
    report_data JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_report_type (report_type),
    INDEX idx_report_date (report_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Default Admin
-- =====================================================
-- Password: admin123 (you should change this!)
-- Admin username: admin
INSERT INTO admins (username, email, password, role) VALUES
('admin', 'admin@mageek.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- =====================================================
-- Insert Sample Network
-- =====================================================
INSERT INTO networks (network_name, network_ip, network_address, network_secret, description, is_active) VALUES
('Home Network', '192.168.1.0/24', '192.168.1.1', 'mysecretkey123', 'Home WiFi Network', TRUE);

-- =====================================================
-- Insert Sample Device
-- =====================================================
INSERT INTO devices (device_name, device_type, ip_address, mac_address, network_id, device_status) VALUES
('Smart Screen 1', 'smart_screen', '192.168.1.10', 'AA:BB:CC:DD:EE:01', 1, 'online'),
('Router Main', 'router', '192.168.1.1', 'AA:BB:CC:DD:EE:FF', 1, 'online');

-- =====================================================
-- Database Schema Complete!
-- =====================================================
