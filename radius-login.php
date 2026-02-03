<?php
/**
 * RADIUS Admin Panel - Login Page
 * =====================================================
 * Mageek's Complete RADIUS System
 * Date: 2026-02-02
 * =====================================================
 */

require_once 'radius-config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$messageType = '';

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $conn = getDBConnection();

        // Get user from database
        $sql = "SELECT * FROM admins WHERE username = :username AND is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Update last login
            $sql = "UPDATE admins SET last_login = NOW() WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $admin['id']]);

            // Set session variables
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];
            $_SESSION['email'] = $admin['email'];

            // Log activity
            logActivity($admin['id'], $admin['username'], 'login');

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $message = 'خطأ في اسم المستخدم أو كلمة المرور';
            $messageType = 'danger';
        }
    } catch (Exception $e) {
        $message = 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة لاحقاً.';
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .login-logo {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .login-title {
            color: #333;
            margin-bottom: 30px;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .copyright {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center">
            <div class="login-logo">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 class="login-title"><?php echo APP_NAME; ?></h2>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">اسم المستخدم</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="أدخل اسم المستخدم" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="أدخل كلمة المرور" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="bi bi-box-arrow-in-right"></i>
                تسجيل الدخول
            </button>
        </form>

        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. جميع الحقوق محفوظة.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
