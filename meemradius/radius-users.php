<?php
/**
 * RADIUS Admin Panel - Users Management
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

// Handle actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        $conn = getDBConnection();

        if ($action === 'add_user') {
            $username = sanitizeInput($_POST['username']);
            $email = sanitizeInput($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $subscription_type = $_POST['subscription_type'];
            $full_name = sanitizeInput($_POST['full_name']);

            // Set subscription dates
            $now = new DateTime();
            switch ($subscription_type) {
                case 'daily':
                    $end_date = clone $now;
                    $end_date->modify('+1 day');
                    break;
                case 'monthly':
                    $end_date = clone $now;
                    $end_date->modify('+1 month');
                    break;
                case 'yearly':
                    $end_date = clone $now;
                    $end_date->modify('+1 year');
                    break;
                case 'lifetime':
                    $end_date = null;
                    break;
            }

            // Insert user
            $sql = "INSERT INTO users (username, email, password, full_name, subscription_type, subscription_status, subscription_start_date, subscription_end_date)
                    VALUES (:username, :email, :password, :full_name, :subscription_type, 'active', NOW(), :end_date)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $full_name,
                'subscription_type' => $subscription_type,
                'end_date' => $end_date
            ]);

            $message = 'تمت إضافة المستخدم بنجاح';
            $messageType = 'success';

        } elseif ($action === 'update_user') {
            $user_id = (int)$_POST['user_id'];
            $username = sanitizeInput($_POST['username']);
            $email = sanitizeInput($_POST['email']);
            $full_name = sanitizeInput($_POST['full_name']);

            $sql = "UPDATE users SET username = :username, email = :email, full_name = :full_name
                    WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $user_id,
                'username' => $username,
                'email' => $email,
                'full_name' => $full_name
            ]);

            $message = 'تم تحديث بيانات المستخدم بنجاح';
            $messageType = 'success';

        } elseif ($action === 'delete_user') {
            $user_id = (int)$_POST['user_id'];

            $sql = "DELETE FROM users WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['user_id' => $user_id]);

            $message = 'تم حذف المستخدم بنجاح';
            $messageType = 'success';

        } elseif ($action === 'reset_password') {
            $user_id = (int)$_POST['user_id'];
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = :password WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $user_id,
                'password' => $new_password
            ]);

            $message = 'تم تغيير كلمة المرور بنجاح';
            $messageType = 'success';
        }
    } catch (Exception $e) {
        $message = 'حدث خطأ: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get all users
try {
    $conn = getDBConnection();
    $stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين - <?php echo APP_NAME; ?></title>

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

        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-suspended {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-canceled {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                        <a class="nav-link" href="radius-dashboard.php">
                            <i class="bi bi-speedometer2"></i> لوحة التحكم
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="radius-users.php">
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
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="radius-logout.php">
                            <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-people-fill"></i> إدارة المستخدمين
                            <span class="badge bg-primary"><?php echo count($users); ?></span>
                        </span>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus"></i> إضافة مستخدم جديد
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (count($users) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>اسم المستخدم</th>
                                            <th>الاسم الكامل</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>الاشتراك</th>
                                            <th>حالة الاشتراك</th>
                                            <th>آخر دخول</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $index => $user): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo $user['username']; ?></strong></td>
                                                <td><?php echo $user['full_name']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td>
                                                    <?php echo ucfirst($user['subscription_type']); ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $user['subscription_status']; ?>">
                                                        <?php echo ucfirst($user['subscription_status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo $user['last_login_time'] ?? 'لم يتم تسجيل الدخول بعد'; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-info"
                                                                data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-warning"
                                                                data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?php echo $user['id']; ?>">
                                                            <i class="bi bi-key"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal<?php echo $user['id']; ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit User Modal -->
                                            <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تعديل بيانات المستخدم</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="update_user">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label">اسم المستخدم</label>
                                                                    <input type="text" name="username" class="form-control"
                                                                           value="<?php echo $user['username']; ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">البريد الإلكتروني</label>
                                                                    <input type="email" name="email" class="form-control"
                                                                           value="<?php echo $user['email']; ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">الاسم الكامل</label>
                                                                    <input type="text" name="full_name" class="form-control"
                                                                           value="<?php echo $user['full_name']; ?>" required>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary w-100">
                                                                    تحديث البيانات
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reset Password Modal -->
                                            <div class="modal fade" id="resetPasswordModal<?php echo $user['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تغيير كلمة المرور</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="reset_password">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label">كلمة المرور الجديدة</label>
                                                                    <input type="password" name="new_password" class="form-control"
                                                                           required>
                                                                </div>
                                                                <button type="submit" class="btn btn-warning w-100">
                                                                    تغيير كلمة المرور
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete User Modal -->
                                            <div class="modal fade" id="deleteUserModal<?php echo $user['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">حذف المستخدم</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>هل أنت متأكد من حذف المستخدم <strong><?php echo $user['username']; ?></strong>؟</p>
                                                            <p class="text-danger">هذه العملية لا يمكن التراجع عنها!</p>
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="delete_user">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="btn btn-danger w-100">
                                                                    نعم، احذف المستخدم
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-people" style="font-size: 3rem;"></i>
                                <p class="mt-3">لا يوجد مستخدمين</p>
                                <button type="button" class="btn btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    إضافة مستخدم جديد
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_user">
                        <div class="mb-3">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نوع الاشتراك</label>
                            <select name="subscription_type" class="form-select" required>
                                <option value="daily">يومي</option>
                                <option value="monthly">شهري</option>
                                <option value="yearly">سنوي</option>
                                <option value="lifetime">للأبد</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            إضافة المستخدم
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
