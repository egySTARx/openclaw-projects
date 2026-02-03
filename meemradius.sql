-- =====================================================
-- MEEMRADIUS Database Schema
-- Pre-configured with secure passwords
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS meemradius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE meemradius;

-- =====================================================
-- Table: admin_users
-- =====================================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'superadmin', 'moderator') DEFAULT 'admin',
    permissions JSON,
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: users
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
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_subscription_status (subscription_status),
    INDEX idx_last_login (last_login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: access_logs
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
    INDEX idx_request_time (request_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: networks
-- =====================================================
CREATE TABLE IF NOT EXISTS networks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    network_name VARCHAR(100) NOT NULL,
    network_ip VARCHAR(45) NOT NULL,
    network_address VARCHAR(45) NOT NULL,
    network_secret VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    vlan_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_network_name (network_name),
    INDEX idx_network_ip (network_ip),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: devices
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
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE SET NULL,
    INDEX idx_device_name (device_name),
    INDEX idx_device_type (device_type),
    INDEX idx_device_status (device_status),
    INDEX idx_ip_address (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: notifications
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
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: settings
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Default Admin Account (Pre-configured)
-- =====================================================
-- Username: admin
-- Password: AdminPass789!
INSERT INTO admin_users (username, email, password, role) VALUES
('admin', 'admin@meemradius.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- =====================================================
-- Default Network
-- =====================================================
INSERT INTO networks (network_name, network_ip, network_address, network_secret, description, is_active) VALUES
('Home Network', '192.168.1.0/24', '192.168.1.1', 'MeemSecret456!', 'Home WiFi Network', TRUE);

-- =====================================================
-- Database Schema Complete!
-- =====================================================
