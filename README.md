# GitHub Projects - Mageek's RADIUS System

Complete RADIUS Management Panel built with PHP and MySQL

## 🚀 Features

- ✅ User Management (Add/Edit/Delete)
- ✅ Subscription System (Daily/Monthly/Yearly/Lifetime)
- ✅ Access Logs (All login attempts with filtering)
- ✅ Network Management (Manage multiple networks)
- ✅ Device Management (Shelly, Zigbee, Smart Screens)
- ✅ Statistics Dashboard (Active users, login counts)
- ✅ Notifications (Expired subscriptions, alerts)
- ✅ Arabic Interface (RTL support)
- ✅ FreeRADIUS Integration

## 📁 Project Structure

```
.
├── radius-admin/           # Admin panel directory
├── scripts/                # Installation scripts
├── radius-config.php       # Database configuration
├── radius-dashboard.php    # Main dashboard
├── radius-login.php        # Login page
├── radius-logout.php       # Logout page
├── radius-profile.php      # User profile
├── radius-users.php        # User management
├── radius-db-schema.sql    # Complete database schema
├── radius-system-guide.md  # Complete installation guide
├── README-RADIUS-PANEL.md  # Detailed documentation
└── raduis-plan.md          # Quick reference
```

## ⚠️ Important Notes

**⚠️ Contains passwords in scripts:**
- `install-radius.sh` - Contains MySQL password for demo
- These are example passwords for installation guide
- **IMPORTANT:** Change all passwords in production!

## 🎯 Installation (Ubuntu 22.04)

### Quick Install

```bash
# 1. Upload all files to your server
cd /var/www/html/radius-panel

# 2. Run installation script
bash install-radius.sh

# 3. Access panel
http://YOUR_IP/radius-panel/radius-login.php

# Default credentials:
# Username: admin
# Password: admin123
```

### Manual Install

```bash
# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Create database
sudo mysql << EOF
CREATE DATABASE radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'radiususer'@'localhost' IDENTIFIED BY 'MageekPass123!';
GRANT ALL PRIVILEGES ON radius.* TO 'radiususer'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

# Import database schema
mysql -u radiususer -p'MageekPass123!' radius < radius-db-schema.sql

# Install FreeRADIUS
sudo apt install freeradius freeradius-mysql -y

# Copy files
sudo mkdir -p /var/www/html/radius-panel
sudo cp * /var/www/html/radius-panel/

# Configure database
nano radius-config.php
# Update password to your MySQL password

# Enable services
sudo a2enmod rewrite
sudo systemctl restart apache2 freeradius
```

## 🔧 Database Schema

**Tables:**
- users (المستخدمين)
- access_logs (سجلات الوصول)
- networks (الشبكات)
- devices (الأجهزة)
- admins (المشرفين)
- notifications (الإشعارات)
- settings (الإعدادات)
- device_logs (سجلات الأجهزة)
- subscription_renewals (تجديد الاشتراكات)
- reports (التقارير)

## 📖 Documentation

- **Complete Guide:** `radius-system-guide.md`
- **Installation:** `README-RADIUS-PANEL.md`
- **Quick Plan:** `raduis-plan.md`

## 🎨 Supported Networks

- FreeRADIUS (Primary)
- NPS (Microsoft Network Policy Server)
- PAM Authentication
- Shelly Integration
- Zigbee2MQTT

## 🌐 Supported Devices

- Smart Screens (Shenzhen Ninebot, etc.)
- Routers & Access Points
- VPN Gateways
- IoT Devices (Shelly, Zigbee)
- Switches

## 🔐 Security Features

- Password Hashing (SHA-256)
- Session Management
- CSRF Protection
- SQL Injection Protection
- Failed Login Tracking
- Account Blocking
- Audit Logs

## 📊 Features Summary

| Feature | Status |
|---------|--------|
| User Management | ✅ Complete |
| Subscription System | ✅ Complete |
| Access Logs | ✅ Complete |
| Network Management | ✅ Complete |
| Device Management | ✅ Complete |
| Statistics | ✅ Complete |
| Notifications | ✅ Complete |
| Arabic Interface | ✅ Complete |
| FreeRADIUS Integration | ✅ Complete |

## 🚀 Quick Start

1. **Upload files** to your web server
2. **Import database** using `radius-db-schema.sql`
3. **Configure database** in `radius-config.php`
4. **Install FreeRADIUS** on the server
5. **Access panel** at `http://YOUR_IP/radius-panel/radius-login.php`

## 📝 Configuration

### Database Connection

Edit `radius-config.php`:

```php
$host = 'localhost';
$dbname = 'radius';
$username = 'radiususer';
$password = 'your_password';
```

### FreeRADIUS Configuration

Edit `/etc/freeradius/3.0/clients.conf`:

```conf
client HomeNetwork {
    ipaddr = 192.168.1.0/24
    secret = mysecret123
    shortname = home_network
}
```

## 🔗 Links

- **FreeRADIUS Docs:** https://freeradius.org/doc/
- **Ubuntu Guide:** https://linuxize.com/post/setup-freeradius-radius-server/
- **Community:** https://freeradius.org/

## 📞 Support

For issues or questions, check the documentation files or visit FreeRADIUS community.

---

**Built for Mageek at Technomeem.com**

**Version:** 1.0
**Last Updated:** 2026-02-03
**License:** Open Source
