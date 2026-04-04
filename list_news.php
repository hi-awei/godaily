<?php
// 获取资讯列表
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$db_host = 'localhost';
$db_name = 'web01_com';
$db_user = 'web01_com';
$db_pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $news = $db->query("SELECT id, title, source, created_at FROM news ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($news, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}