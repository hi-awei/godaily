<?php
// 检查表结构
$host = '127.0.0.1';
$dbname = 'web01_com';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    // 查看tools表结构
    $cols = $db->query("SHOW COLUMNS FROM tools")->fetchAll();
    echo "tools表字段:\n";
    foreach ($cols as $c) {
        echo "- " . $c['Field'] . " (" . $c['Type'] . ")\n";
    }
    
    echo "\n---\n";
    
    // 查看news表结构
    $cols2 = $db->query("SHOW COLUMNS FROM news")->fetchAll();
    echo "news表字段:\n";
    foreach ($cols2 as $c) {
        echo "- " . $c['Field'] . " (" . $c['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}