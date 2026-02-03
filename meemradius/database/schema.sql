-- ============================================
-- RADIUS ADMIN PANEL - DATABASE SCHEMA
-- Arabic Comments (تعليقات باللغة العربية)
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS radius_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE radius_admin;

-- ============================================
-- USERS TABLE (جدول المستخدمين)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    ip_address VARCHAR(45),
    subscription_type ENUM('daily', 'monthly', 'yearly') NOT NULL DEFAULT 'monthly',
    subscription_start DATE NOT NULL,
    subscription_end DATE NOT NULL,
    status ENUM('active', 'suspended', 'expired') NOT NULL DEFAULT 'active',
    login_attempts INT DEFAULT 0,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    INDEX idx_status (status),
    INDEX idx_username (username),
    INDEX idx_subscription_end (subscription_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SUBSCRIPTIONS TABLE (جدول الاشتراكات)
-- ============================================
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('daily', 'monthly', 'yearly') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_end_date (end_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- RADIUS USERS TABLE (جدول مستخدمي RADIUS)
-- ============================================
CREATE TABLE IF NOT EXISTS radius_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    group_id INT,
    expiration_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- NETWORKS TABLE (جدول الشبكات)
-- ============================================
CREATE TABLE IF NOT EXISTS networks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    vlan_id INT,
    ip_range_start VARCHAR(45),
    ip_range_end VARCHAR(45),
    gateway VARCHAR(45),
    dns_servers VARCHAR(255),
    secret VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ACCESS LOGS TABLE (جدول سجلات الوصول)
-- ============================================
CREATE TABLE IF NOT EXISTS access_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45),
    action ENUM('login', 'logout', 'failure', 'success', 'revoke') NOT NULL,
    status ENUM('success', 'failure') NOT NULL,
    reason VARCHAR(255),
    mac_address VARCHAR(17),
    network_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE SET NULL,
    INDEX idx_username (username),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_address (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- NOTIFICATIONS TABLE (جدول الإشعارات)
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('subscription_expiry', 'subscription_expired', 'failed_login', 'password_reset', 'system_alert') NOT NULL,
    message TEXT NOT NULL,
    is_read ENUM('yes', 'no') DEFAULT 'no',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ADMIN USERS TABLE (جدول المشرفين)
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('super_admin', 'admin', 'moderator') NOT NULL DEFAULT 'admin',
    permissions JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEVICES TABLE (جدول الأجهزة)
-- ============================================
CREATE TABLE IF NOT EXISTS devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('shelly', 'zigbee', 'other') NOT NULL,
    manufacturer VARCHAR(100),
    model VARCHAR(100),
    ip_address VARCHAR(45),
    mac_address VARCHAR(17),
    serial_number VARCHAR(100),
    status ENUM('online', 'offline', 'error') DEFAULT 'offline',
    location VARCHAR(255),
    network_id INT,
    last_seen DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_mac_address (mac_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEVICE SENSORS TABLE (جدول مستشعرات الأجهزة)
-- ============================================
CREATE TABLE IF NOT EXISTS device_sensors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_id INT NOT NULL,
    sensor_type VARCHAR(50) NOT NULL,
    value DECIMAL(10, 2),
    unit VARCHAR(20),
    unit_price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE,
    INDEX idx_device_id (device_id),
    INDEX idx_sensor_type (sensor_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SHELLY INTEGRATION TABLE (جدول تكامل Shelly)
-- ============================================
CREATE TABLE IF NOT EXISTS shelly_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_id INT NOT NULL,
    shelly_id VARCHAR(100) NOT NULL,
    device_name VARCHAR(100),
    device_type VARCHAR(50),
    control_type ENUM('on_off', 'dimmer', 'switch', 'energy') NOT NULL,
    module_id VARCHAR(50),
    pin_out INT,
    pin_in INT,
    is_master ENUM('yes', 'no') DEFAULT 'no',
    master_device_id INT,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE,
    INDEX idx_shelly_id (shelly_id),
    INDEX idx_device_id (device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ZIGBEE2MQTT INTEGRATION TABLE (جدول تكامل Zigbee2MQTT)
-- ============================================
CREATE TABLE IF NOT EXISTS zigbee_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_id INT NOT NULL,
    ieee_address VARCHAR(50) NOT NULL UNIQUE,
    friendly_name VARCHAR(100),
    type VARCHAR(50),
    manufacturer VARCHAR(100),
    model VARCHAR(100),
    power_source VARCHAR(50),
    battery_level INT,
    firmware_version VARCHAR(50),
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE,
    INDEX idx_ieee_address (ieee_address),
    INDEX idx_friendly_name (friendly_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- LOGIN ATTEMPTS TABLE (جدول محاولات تسجيل الدخول)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45),
    success ENUM('yes', 'no') NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_ip_address (ip_address),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SETTINGS TABLE (جدول الإعدادات)
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    key_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (key_name, key_value, description) VALUES
('site_name', 'RADIUS Admin Panel', 'اسم الموقع'),
('site_description', 'لوحة إدارة RADIUS الكاملة', 'وصف الموقع'),
('timezone', 'UTC', 'المنطقة الزمنية'),
('language', 'ar', 'اللغة'),
('currency', 'SAR', 'العملة'),
('price_daily', '10', 'سعر الاشتراك اليومي'),
('price_monthly', '50', 'سعر الاشتراك الشهري'),
('price_yearly', '500', 'سعر الاشتراك السنوي'),
('max_login_attempts', '5', 'أقصى محاولات تسجيل دخول'),
('login_lockout_time', '15', 'مدة حظر الحساب بالدقائق'),
('auto_renew_subscriptions', 'yes', 'الإشتراكات التلقائية'),
('daily_report_time', '08:00', 'وقت إرسال التقرير اليومي'),
('subscription_expiry_days', '3', 'أيام إشعار نفاذ الاشتراك');

-- Insert default admin user (admin123)
INSERT INTO admins (username, password, full_name, email, role, permissions)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المشرف العام', 'admin@radius.local', 'super_admin', '{"users":true,"subscriptions":true,"logs":true,"networks":true,"devices":true,"stats":true,"admin":true,"reports":true}');

-- Insert default network
INSERT INTO networks (name, description, vlan_id, ip_range_start, ip_range_end, gateway, dns_servers, secret, status)
VALUES ('Main Network', 'الشبكة الرئيسية', 1, '192.168.1.10', '192.168.1.200', '192.168.1.1', '8.8.8.8, 8.8.4.4', 'secret123', 'active');

-- ============================================
-- VIEWS (View اختصارات لوصول سريع)
-- ============================================

-- Users view (عرض المستخدمين)
CREATE OR REPLACE VIEW users_view AS
SELECT
    u.id,
    u.username,
    u.email,
    u.full_name,
    u.phone,
    u.ip_address,
    s.type AS subscription_type,
    s.start_date,
    s.end_date,
    s.status AS subscription_status,
    u.status AS user_status,
    u.login_attempts,
    u.last_login,
    u.created_at
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id AND s.status = 'active'
ORDER BY u.created_at DESC;

-- Network summary view (عرض ملخص الشبكة)
CREATE OR REPLACE VIEW network_summary AS
SELECT
    n.id,
    n.name,
    n.description,
    COUNT(DISTINCT u.id) AS active_users,
    COUNT(DISTINCT ar.id) AS active_radius_users,
    COUNT(DISTINCT d.id) AS active_devices
FROM networks n
LEFT JOIN users u ON n.id = u.network_id AND u.status = 'active'
LEFT JOIN radius_users ar ON n.id = ar.group_id
LEFT JOIN devices d ON n.id = d.network_id AND d.status = 'online'
GROUP BY n.id;

-- Statistics view (عرض الإحصائيات)
CREATE OR REPLACE VIEW statistics_view AS
SELECT
    COUNT(DISTINCT u.id) AS total_users,
    COUNT(DISTINCT CASE WHEN u.status = 'active' THEN u.id END) AS active_users,
    COUNT(DISTINCT CASE WHEN u.status = 'expired' THEN u.id END) AS expired_users,
    COUNT(DISTINCT CASE WHEN s.type = 'daily' THEN s.user_id END) AS daily_subscriptions,
    COUNT(DISTINCT CASE WHEN s.type = 'monthly' THEN s.user_id END) AS monthly_subscriptions,
    COUNT(DISTINCT CASE WHEN s.type = 'yearly' THEN s.user_id END) AS yearly_subscriptions,
    COUNT(DISTINCT CASE WHEN d.status = 'online' THEN d.id END) AS online_devices,
    COUNT(DISTINCT CASE WHEN d.status = 'offline' THEN d.id END) AS offline_devices,
    COUNT(DISTINCT ar.id) AS total_radius_users,
    COUNT(DISTINCT al.id) AS total_access_logs,
    COUNT(DISTINCT al.id) FILTER (WHERE al.status = 'success') AS successful_logins,
    COUNT(DISTINCT al.id) FILTER (WHERE al.status = 'failure') AS failed_logins,
    COUNT(DISTINCT CASE WHEN DATE(al.created_at) = CURRENT_DATE THEN al.id END) AS today_logins,
    COUNT(DISTINCT CASE WHEN DATE(al.created_at) = CURRENT_DATE AND al.status = 'success' THEN al.id END) AS today_success_logins
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id
LEFT JOIN devices d ON u.network_id = d.network_id
LEFT JOIN radius_users ar ON u.id = ar.id
LEFT JOIN access_logs al ON u.id = al.user_id;
