#!/bin/bash
# Non-interactive installation script for RADIUS on Ubuntu 22.04

echo "Starting RADIUS Installation..."

# Step 1-2: Update and Install MySQL (non-interactive)
echo "Installing MySQL..."
DEBIAN_FRONTEND=noninteractive apt install -y mysql-server

echo "Starting MySQL service..."
systemctl start mysql
systemctl enable mysql

# Step 3: Configure MySQL (non-interactive)
echo "Configuring MySQL..."
mysql -u root << EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'AdminPassword123!';
FLUSH PRIVILEGES;
EOF

# Step 4: Create Database and User
echo "Creating RADIUS database and user..."
mysql -u root -p'AdminPassword123!' << EOF
CREATE DATABASE IF NOT EXISTS radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'radiususer'@'localhost' IDENTIFIED BY 'MageekPass123!';
GRANT ALL PRIVILEGES ON radius.* TO 'radiususer'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

# Step 5: Install FreeRADIUS
echo "Installing FreeRADIUS..."
apt install -y freeradius freeradius-mysql

# Step 6: Import FreeRADIUS Schema
echo "Importing FreeRADIUS schema..."
mysql -u radiususer -p'MageekPass123!' radius < /usr/share/doc/freeradius/examples/sql/mysql/mysql.schema.sql

# Step 7: Import Your Schema
echo "Importing Mageek's database schema..."
mysql -u root -p'AdminPassword123!' radius < /root/.openclaw/workspace/radius-db-schema.sql

# Step 8: Enable Services
echo "Starting Apache and FreeRADIUS..."
systemctl enable apache2 freeradius
systemctl start apache2 freeradius

# Step 9: Create Directory
echo "Creating RADIUS panel directory..."
mkdir -p /var/www/html/radius-panel
chown -R $USER:$USER /var/www/html/radius-panel
chmod -R 755 /var/www/html/radius-panel

# Step 10: Copy Files
echo "Copying PHP and SQL files..."
cd /root/.openclaw/workspace
cp *.php *.sql /var/www/html/radius-panel/

# Step 11: Create Config File
echo "Creating database config..."
cd /var/www/html/radius-panel
cat > radius-config.php << 'EOF'
<?php
$host = 'localhost';
$dbname = 'radius';
$username = 'radiususer';
$password = 'MageekPass123!';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
?>
EOF

# Step 12: Create .htaccess
echo "Creating .htaccess..."
cat > .htaccess << 'EOF'
Options -Indexes
<FilesMatch "\.(sql|env)$">
    Order allow,deny
    Deny from all
</FilesMatch>
EOF

# Step 13: Enable Rewrite
echo "Enabling Apache rewrite module..."
a2enmod rewrite
systemctl restart apache2

# Step 14: Configure FreeRADIUS
echo "Configuring FreeRADIUS clients..."
cat >> /etc/freeradius/3.0/clients.conf << 'EOF'

client HomeNetwork {
    ipaddr = 192.168.1.0/24
    secret = mysecretkey123
    shortname = home_network
    limit = none
}
EOF

# Step 15: Test FreeRADIUS
echo "Testing FreeRADIUS..."
systemctl status freeradius --no-pager | head -20

echo "Testing FreeRADIUS connection..."
radtest admin admin123 127.0.0.1 0 testing123 || echo "Note: This might fail without test user in DB"

echo "=========================================="
echo "=== Installation Complete! ==="
echo "=========================================="
echo ""
echo "Access your panel at:"
echo "http://10.0.0.27/radius-panel/radius-login.php"
echo ""
echo "Default credentials:"
echo "Username: admin"
echo "Password: admin123"
echo ""
echo "Database connection:"
echo "Host: localhost"
echo "Database: radius"
echo "User: radiususer"
echo "Password: MageekPass123!"
echo ""
echo "MySQL root password: AdminPassword123!"
echo ""
echo "FreeRADIUS client secret: mysecretkey123"
echo "=========================================="
