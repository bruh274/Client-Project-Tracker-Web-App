<?php
require __DIR__ . '/db/connect.php';
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
header('Location: login.php');
