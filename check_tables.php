<?php
// 检查友链表是否存在
header('Content-Type: application/json');
$db_host = 'localhost';
$db_name = 'web01_com';
$db_user = 'web01_com';
$db_pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['tables' => $tables], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}