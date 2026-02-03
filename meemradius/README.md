# 🚀 MEEMRADIUS - Complete RADIUS System

**Pre-configured with all passwords - No changes needed!**

## ✨ Features

- ✅ User Management (Add/Edit/Delete)
- ✅ Subscription System (Daily/Monthly/Yearly/Lifetime)
- ✅ Access Logs (Complete logging with filtering)
- ✅ Network Management (Multiple networks support)
- ✅ Device Management (Shelly, Zigbee, Smart Screens)
- ✅ Statistics Dashboard (Charts & Analytics)
- ✅ Notifications (Expired subscriptions, alerts)
- ✅ Arabic Interface (Full RTL support)
- ✅ FreeRADIUS Integration (Complete setup)

## 📋 Quick Start (3 Minutes)

### 1. Upload Files

Upload all files from `meemradius/` to your web server:
```bash
# Upload via FTP, SFTP, or Git
scp -r meemradius/* user@server:/var/www/html/
```

### 2. Run Installation

```bash
cd /var/www/html/meemradius
chmod +x scripts/install.sh
bash scripts/install.sh
```

### 3. Access Panel

Open in browser:
```
http://YOUR_IP/meemradius
```

**Default Login:**
- Username: `admin`
- Password: `AdminPass789!`

## 🔐 Security Configuration (Pre-configured)

| Setting | Value |
|---------|-------|
| **Database Password** | `MeemRadius123!` |
| **FreeRADIUS Secret** | `MeemSecret456!` |
| **Admin Password** | `AdminPass789!` |
| **Session Timeout** | 2 hours |
| **Database Name** | `meemradius` |

**All passwords are secure and pre-configured!**

## 📁 Project Structure

```
meemradius/
├── config/                  # Configuration files
│   ├── database.php        # Database connection (pre-configured)
│   ├── radius.php          # FreeRADIUS settings (pre-configured)
│   └── admin.php           # Admin settings (pre-configured)
├── includes/               # Core files
│   ├── auth.php            # Authentication
│   └── functions.php       # Helper functions
├── admin/                  # Admin panel
├── public/                 # Public pages
│   ├── login.php           # Login page
│   ├── logout.php          # Logout page
│   └── index.php           # Home page
├── radius-users.php        # User management
├── radius-dashboard.php    # Main dashboard
├── radius-db-schema.sql    # Database schema (pre-configured)
├── scripts/
│   ├── install.sh          # Installation script
│   └── setup.sh            # Setup script
├── meemradius.sql          # Complete database schema
├── docs/
│   ├── README.md           # This file
│   ├── INSTALL.md          # Detailed installation
│   └── API.md              # API documentation
└── .gitignore
```

## 🗄️ Database Schema

**10 Tables:**
1. `admin_users` - Admin accounts
2. `users` - Regular users
3. `access_logs` - Login attempts
4. `networks` - Network configurations
5. `devices` - Device management
6. `notifications` - User alerts
7. `settings` - System settings
8. `device_logs` - Device activity
9. `subscription_renewals` - Renewal tracking
10. `reports` - Statistics & analytics

## 🌐 Supported Devices & Networks

### Networks
- FreeRADIUS (Primary)
- NPS (Microsoft)
- PAM Authentication

### Devices
- Smart Screens (Shenzhen Ninebot, etc.)
- Routers & Access Points
- VPN Gateways
- IoT Devices (Shelly, Zigbee)
- Switches

## 📊 Features Summary

| Feature | Status |
|---------|--------|
| User Management | ✅ Complete |
| Subscription System | ✅ Complete |
| Access Logs | ✅ Complete |
| Network Management | ✅ Complete |
| Device Management | ✅ Complete |
| Statistics Dashboard | ✅ Complete |
| Notifications | ✅ Complete |
| Arabic Interface | ✅ Complete |
| FreeRADIUS Integration | ✅ Complete |
| Installation Script | ✅ Complete |

## 🚀 After Installation

### Change Passwords (Recommended)

```bash
# 1. Login to panel
http://YOUR_IP/meemradius

# 2. Go to Admin → Settings
# 3. Change admin password
# 4. Change database password
```

### Test FreeRADIUS

```bash
# Test connection
echo -e "User-Password = AdminPass789!\nUser-Name = admin\nCalling-Station-Id = test" | radtest admin AdminPass789! 127.0.0.1 0 testing123

# Should see: Received Access-Accept
```

## 🛠️ Manual Installation (If Needed)

### Install MySQL
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### Create Database
```bash
sudo mysql << EOF
CREATE DATABASE meemradius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'meemradius_admin'@'localhost' IDENTIFIED BY 'MeemRadius123!';
GRANT ALL PRIVILEGES ON meemradius.* TO 'meemradius_admin'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### Import Schema
```bash
mysql -u meemradius_admin -p'MeemRadius123!' meemradius < meemradius.sql
```

### Install FreeRADIUS
```bash
sudo apt install freeradius freeradius-mysql -y
mysql -u meemradius_admin -p'MeemRadius123!' meemradius < /usr/share/doc/freeradius/examples/sql/mysql/mysql.schema.sql
```

### Setup Directory
```bash
sudo mkdir -p /var/www/html/meemradius
sudo cp -r * /var/www/html/meemradius/
sudo chown -R www-data:www-data /var/www/html/meemradius
chmod -R 755 /var/www/html/meemradius
```

### Configure Apache
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Enable Services
```bash
sudo systemctl start freeradius
sudo systemctl enable freeradius
```

## 🔧 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/users` | GET/POST | Get/create users |
| `/api/users/:id` | GET/PUT/DELETE | Get/update/delete user |
| `/api/networks` | GET/POST | Get/create networks |
| `/api/devices` | GET/POST | Get/create devices |
| `/api/logs` | GET | Get access logs |
| `/api/stats` | GET | Get statistics |

See `docs/API.md` for complete API documentation.

## 📚 Documentation

- **README.md** - This file (Quick start)
- **INSTALL.md** - Detailed installation guide
- **API.md** - API documentation
- **docs/database.md** - Database schema documentation

## 🎨 Admin Panel Features

### Dashboard
- Active users count
- Today's logins
- Network status
- Device online/offline

### User Management
- Add/Edit/Delete users
- Set subscription types
- Set subscription duration
- Track login attempts

### Network Management
- Add/Edit/Delete networks
- Configure IPs
- Set secrets
- Manage VLANs

### Device Management
- Add/Edit/Delete devices
- Monitor device status
- Track online/offline
- Device types: Smart Screen, Router, AP, Shelly, Zigbee, VPN, Switch

### Reports
- Daily/Weekly/Monthly reports
- Export functionality
- Charts & graphs

## 🌍 Language Support

- **Primary:** Arabic (RTL)
- **Secondary:** English (LTR)
- Change in: Settings → Language

## 📞 Support

### Check Logs
```bash
# Apache logs
tail -f /var/log/apache2/error.log

# FreeRADIUS logs
tail -f /var/log/freeradius/radius.log

# MeemRadius logs
tail -f /var/www/html/meemradius/logs/error.log
```

### Common Issues

**Cannot access panel:**
```bash
# Check Apache
sudo systemctl status apache2
sudo systemctl restart apache2

# Check permissions
ls -la /var/www/html/meemradius/
```

**Database connection error:**
```bash
# Check MySQL
sudo systemctl status mysql
mysql -u meemradius_admin -p'MeemRadius123!' meemradius

# Check config
cat /var/www/html/meemradius/config/database.php
```

**FreeRADIUS not working:**
```bash
# Check service
sudo systemctl status freeradius
sudo systemctl restart freeradius

# Check config
cat /etc/freeradius/3.0/clients.conf
```

## 🎯 What's Next?

1. **Login to panel** at `http://YOUR_IP/meemradius`
2. **Change default password** (AdminPass789!)
3. **Add your first user**
4. **Configure your network** (FreeRADIUS secret: MeemSecret456!)
5. **Add devices** (Shelly, Smart Screens, etc.)
6. **Start managing** your RADIUS system!

## 📈 Next Steps

### Development Roadmap

- [ ] Mobile app
- [ ] WhatsApp notifications
- [ ] Email integration
- [ ] API for third-party
- [ ] Custom reports
- [ ] Dashboard themes

### Integration Options

- [ ] Integration with existing RADIUS
- [ ] Shelly device integration
- [ ] Zigbee2MQTT support
- [ ] VPN gateways
- [ ] Smart home integration

---

**Built for Mageek at Technomeem.com**

**Version:** 1.0.0
**Last Updated:** 2026-02-03
**License:** Open Source (MIT)

**⚠️ Important:** Change default passwords after first login!

---

## 🎉 You're Ready!

**No configuration needed!** All passwords are pre-configured.

Just upload and run the installation script!

🚀 **Happy Radius Management!**
