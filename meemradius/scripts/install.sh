#!/bin/bash
# MEEMRADIUS Installation Script
# All passwords pre-configured - No changes needed!

echo "==================================="
echo "MEEMRADIUS Installation"
echo "==================================="
echo ""

# Configuration
DB_USER="meemradius_admin"
DB_PASS="MeemRadius123!"
RADIUS_SECRET="MeemSecret456!"
ADMIN_PASS="AdminPass789!"

# Step 1: Install MySQL
echo "📦 Step 1: Installing MySQL..."
DEBIAN_FRONTEND=noninteractive apt install -y mysql-server
systemctl start mysql
systemctl enable mysql
echo "✅ MySQL installed and started"

# Step 2: Create Database and User
echo ""
echo "📦 Step 2: Creating database..."
mysql -u root << EOF
CREATE DATABASE IF NOT EXISTS meemradius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON meemradius.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF
echo "✅ Database and user created"

# Step 3: Import Database Schema
echo ""
echo "📦 Step 3: Importing database schema..."
mysql -u $DB_USER -p$DB_PASS meemradius < meemradius.sql
echo "✅ Database schema imported"

# Step 4: Install FreeRADIUS
echo ""
echo "📦 Step 4: Installing FreeRADIUS..."
apt install -y freeradius freeradius-mysql
mysql -u $DB_USER -p$DB_PASS meemradius < /usr/share/doc/freeradius/examples/sql/mysql/mysql.schema.sql
echo "✅ FreeRADIUS installed"

# Step 5: Configure FreeRADIUS
echo ""
echo "📦 Step 5: Configuring FreeRADIUS..."
cat >> /etc/freeradius/3.0/clients.conf << EOF

client MeemRadiusNetwork {
    ipaddr = 192.168.1.0/24
    secret = $RADIUS_SECRET
    shortname = meem_radius
    limit = none
}
EOF
echo "✅ FreeRADIUS configured"

# Step 6: Install Apache and PHP
echo ""
echo "📦 Step 6: Installing Apache and PHP..."
apt install -y apache2 php libapache2-mod-php php-mysql php-gd php-xml php-curl php-mbstring php-zip php-json php-ldap php-session
systemctl enable apache2
systemctl start apache2
echo "✅ Apache and PHP installed"

# Step 7: Setup Directory
echo ""
echo "📦 Step 7: Setting up directory..."
mkdir -p /var/www/html/meemradius
chown -R www-data:www-data /var/www/html/meemradius
chmod -R 755 /var/www/html/meemradius
echo "✅ Directory set up"

# Step 8: Copy Files
echo ""
echo "📦 Step 8: Copying files..."
cp -r * /var/www/html/meemradius/
echo "✅ Files copied"

# Step 9: Enable Rewrite
echo ""
echo "📦 Step 9: Enabling Apache rewrite..."
a2enmod rewrite
systemctl restart apache2
echo "✅ Rewrite enabled"

# Step 10: Create Config Files
echo ""
echo "📦 Step 10: Creating configuration files..."

# Database config
cat > /var/www/html/meemradius/config/database.php << CONFIGEOF
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'meemradius');
define('DB_USER', '$DB_USER');
define('DB_PASS', '$DB_PASS');
define('BASE_URL', '/meemradius');
?>
CONFIGEOF

# FreeRADIUS config
cat > /var/www/html/meemradius/config/radius.php << CONFIGEOF
<?php
define('RADIUS_SERVER', '127.0.0.1');
define('RADIUS_SECRET', '$RADIUS_SECRET');
define('PORT', '1812');
?>
CONFIGEOF

# Admin config
cat > /var/www/html/meemradius/config/admin.php << CONFIGEOF
<?php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '$ADMIN_PASS');
define('SESSION_TIMEOUT', 7200);
?>
CONFIGEOF

echo "✅ Configuration files created"

# Step 11: Update permissions
echo ""
echo "📦 Step 11: Updating permissions..."
chmod 644 /var/www/html/meemradius/*.php
chmod 644 /var/www/html/meemradius/config/*.php
chmod 755 /var/www/html/meemradius/scripts/*.sh

# Step 12: Test
echo ""
echo "==================================="
echo "✅ Installation Complete!"
echo "==================================="
echo ""
echo "📊 Access Information:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🚀 Access URL:"
echo "   http://YOUR_IP/meemradius"
echo ""
echo "🔐 Default Credentials:"
echo "   Username: admin"
echo "   Password: $ADMIN_PASS"
echo ""
echo "🗄️  Database:"
echo "   Host: localhost"
echo "   Database: meemradius"
echo "   Username: $DB_USER"
echo "   Password: $DB_PASS"
echo ""
echo "📡 FreeRADIUS:"
echo "   Server: 127.0.0.1:1812"
echo "   Secret: $RADIUS_SECRET"
echo ""
echo "==================================="
echo ""
echo "🎉 You can now access MeemRadius!"
echo "==================================="
echo ""
