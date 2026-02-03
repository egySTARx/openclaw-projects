<?php
/**
 * RADIUS Admin Panel - Logout
 * =====================================================
 */

session_start();
session_destroy();

header('Location: radius-login.php');
exit();
