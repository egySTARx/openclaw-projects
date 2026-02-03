# MEEMRADIUS - Complete Configuration

## 🔐 Security Configuration

**Database Password (MySQL):**
```
MeemRadius123!
```

**FreeRADIUS Secret:**
```
MeemSecret456!
```

**Admin Account:**
```
Username: admin
Password: AdminPass789!
```

**Session Password:**
```
MeemSession@123
```

## 📁 File Organization

```
meemradius/
├── config/
│   ├── database.php        # Database connection
│   ├── radius.php          # FreeRADIUS configuration
│   └── system.php          # System settings
├── includes/
│   ├── auth.php            # Authentication
│   ├── functions.php       # Helper functions
│   └── header.php          # HTML header
├── admin/                  # Admin panel
├── public/                 # Public pages
│   ├── index.php
│   ├── login.php
│   └── logout.php
├── api/                    # REST API
├── scripts/
│   ├── install.sh          # Installation script
│   └── setup.sh            # Setup script
├── docs/
│   ├── README.md
│   ├── INSTALL.md
│   └── API.md
├── meemradius.sql          # Complete database schema
└── .gitignore
```

## 🚀 Quick Start

1. Clone repository
2. Run `./scripts/install.sh`
3. Access at `http://your-ip/meemradius`

All passwords are pre-configured!
