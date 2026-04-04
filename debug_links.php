<?php
// 测试 admin/links.php 的错误
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../includes/functions.php';

// 检查 requireAuth 是否存在
if (!function_exists('requireAuth')) {
    echo "requireAuth 函数不存在!";
    exit;
}

requireAuth();
echo "OK";