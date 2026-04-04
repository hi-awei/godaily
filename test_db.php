<?php
/**
 * 测试数据库连接和查询
 */
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== 数据库连接测试 ===\n\n";

echo "DB变量: ";
var_dump(isset($db) ? $db : null);
echo "\n";

if ($db) {
    echo "查询工具数量: ";
    try {
        $count = $db->query("SELECT COUNT(*) FROM tools WHERE status = 1")->fetchColumn();
        echo $count . "\n";
    } catch (Exception $e) {
        echo "错误: " . $e->getMessage() . "\n";
    }
    
    echo "查询资讯数量: ";
    try {
        $count = $db->query("SELECT COUNT(*) FROM news WHERE status = 1")->fetchColumn();
        echo $count . "\n";
    } catch (Exception $e) {
        echo "错误: " . $e->getMessage() . "\n";
    }
    
    echo "查询分类数量: ";
    try {
        $count = $db->query("SELECT COUNT(*) FROM tool_categories")->fetchColumn();
        echo $count . "\n";
    } catch (Exception $e) {
        echo "错误: " . $e->getMessage() . "\n";
    }
} else {
    echo "数据库未连接!\n";
    echo "请检查 includes/db.php 配置\n";
}