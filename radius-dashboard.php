<?php
/**
 * RADIUS Admin Panel - Dashboard
 * =====================================================
 * Mageek's Complete RADIUS System
 * Date: 2026-02-02
 * =====================================================
 */

require_once 'radius-config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: radius-login.php');
    exit();
}

// Get user statistics
$stats = getUserStatistics();
$recentActivity = getRecentActivity(5);

// Get active notifications
try {
    $conn = getDBConnection();
    $stmt = $conn->query("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5");
    $notifications = $stmt->fetchAll();
} catch (Exception $e) {
    $notifications = [];
}

// Get recent users
try {
    $stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
    $recentUsers = $stmt->fetchAll();
} catch (Exception $e) {
    $recentUsers = [];
}

// Get active devices
try {
    $stmt = $conn->query("SELECT * FROM devices WHERE device_status = 'online' ORDER BY last_seen DESC LIMIT 10");
    $activeDevices = $stmt->fetchAll();
} catch (Exception $e) {
    $activeDevices = [];
}

// Get network statistics
try {
    $stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
                         FROM networks");
    $networkStats = $stmt->fetch();
} catch (Exception $e) {
    $networkStats = ['total' => 0, 'active' => 0];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 700;
            color: #333;
        }

        .stat-card {
            border-radius: 12px;
            color: white;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.8;
        }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .activity-item {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .notification-item {
            padding: 12px;
            border-left: 3px solid;
            background-color: #f8f9fa;
            margin-bottom: 10px;
        }

        .notification-item.expired {
            border-left-color: #dc3545;
        }

        .notification-item.warning {
            border-left-color: #ffc107;
        }

        .notification-item.success {
            border-left-color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="radius-dashboard.php">
                <i class="bi bi-shield-check"></i> <?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="radius-dashboard.php">
                            <i class="bi bi-speedometer2"></i> لوحة التحكم
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radius-users.php">
                            <i class="bi bi-people"></i> المستخدمين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radius-subscriptions.php">
                            <i class="bi bi-calendar-check"></i> الاشتراكات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radius-networks.php">
                            <i class="bi bi-router"></i> الشبكات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radius-devices.php">
                            <i class="bi bi-grid"></i> الأجهزة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radius-logs.php">
                            <i class="bi bi-journal-text"></i> السجلات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="radius-notifications.php">
                            <i class="bi bi-bell"></i> الإشعارات
                            <?php if (count($notifications) > 0): ?>
                                <span class="badge bg-danger"><?php echo count($notifications); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="radius-profile.php">
                                    <i class="bi bi-person"></i> الملف الشخصي
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="radius-logout.php">
                                    <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card stat-card bg-primary">
                    <div class="card-body">
                        <i class="bi bi-people stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="stat-label">إجمالي المستخدمين النشطين</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success">
                    <div class="card-body">
                        <i class="bi bi-person-plus stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['new_users']); ?></div>
                        <div class="stat-label">مستخدمون جدد هذا الشهر</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <i class="bi bi-clock stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['expiring_soon']); ?></div>
                        <div class="stat-label">اشتراكات تنتهي خلال 7 أيام</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-danger">
                    <div class="card-body">
                        <i class="bi bi-shield-x stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['blocked_users']); ?></div>
                        <div class="stat-label">حسابات محظورة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="row mt-4">
            <!-- Recent Activity -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history"></i> آخر النشاطات
                    </div>
                    <div class="card-body">
                        <?php if (count($recentActivity) > 0): ?>
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-person"></i>
                                            <strong><?php echo $activity['username']; ?></strong>
                                            <?php echo $activity['service']; ?>
                                            <span class="badge bg-<?php echo $activity['status'] === 'success' ? 'success' : 'danger'; ?>">
                                                <?php echo $activity['status']; ?>
                                            </span>
                                        </div>
                                        <div class="activity-time">
                                            <?php echo $activity['request_time']; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                لا يوجد نشاطات حديثة
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Network Statistics -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-router"></i> إحصائيات الشبكات
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <h3><?php echo number_format($networkStats['total']); ?></h3>
                                <p class="text-muted">إجمالي الشبكات</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h3 class="text-success"><?php echo number_format($networkStats['active']); ?></h3>
                                <p class="text-muted">شبكات نشطة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Active Devices -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-grid-3x3-gap-fill"></i> الأجهزة النشطة
                    </div>
                    <div class="card-body">
                        <?php if (count($activeDevices) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($activeDevices as $device): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-cpu"></i>
                                                <strong><?php echo $device['device_name']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $device['device_type']; ?></small>
                                            </div>
                                            <span class="badge bg-success">نشط</span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                لا توجد أجهزة نشطة
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bell"></i> الإشعارات</span>
                        <?php if (count($notifications) > 0): ?>
                            <a href="radius-notifications.php" class="btn btn-sm btn-link">عرض الكل</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item <?php echo $notification['notification_type']; ?>">
                                    <small class="text-muted">
                                        <?php echo $notification['created_at']; ?>
                                    </small>
                                    <p class="mb-0 mt-1">
                                        <?php echo $notification['message']; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                لا توجد إشعارات جديدة
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
