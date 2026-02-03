<?php
/**
 * RADIUS Admin Panel - Profile
 * =====================================================
 */

require_once 'radius-config.php';

if (!isLoggedIn()) {
    header('Location: radius-login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الملف الشخصي - <?php echo APP_NAME; ?></title>
    <!-- Include styles... -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Profile content... -->
    <div class="container py-5">
        <h2>الملف الشخصي: <?php echo $_SESSION['username']; ?></h2>
        <p>البريد الإلكتروني: <?php echo $_SESSION['email']; ?></p>
        <p>الدور: <?php echo $_SESSION['role']; ?></p>
    </div>
</body>
</html>
