<?php
// 查看news表结构
header('Content-Type: application/json');
$db_host = 'localhost';
$db_name = 'web01_com';
$db_user = 'web01_com';
$db_pass = '3FT7Ppatfp19XbAh';

$db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
$cols = $db->query("DESCRIBE news")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols, JSON_PRETTY_PRINT);