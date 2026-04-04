<?php
/**
 * 调试数据库连接
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== 调试开始 ===\n\n";

// 检查 config.php
echo "1. 检查 config.php:\n";
$configPath = __DIR__ . '/config.php';
if (file_exists($configPath)) {
    echo "  ✓ config.php 存在\n";
    
    // 尝试包含
    require_once $configPath;
    echo "  ✓ DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '未定义') . "\n";
    echo "  ✓ DB_NAME: " . (defined('DB_NAME') ? DB_NAME : '未定义') . "\n";
    echo "  ✓ DB_USER: " . (defined('DB_USER') ? DB_USER : '未定义') . "\n";
    echo "  ✓ DB_PASS: " . (defined('DB_PASS') ? '******' : '未定义') . "\n";
} else {
    echo "  ✗ config.php 不存在\n";
}

echo "\n2. 检查 db.php:\n";
$dbPath = __DIR__ . '/includes/db.php';
if (file_exists($dbPath)) {
    echo "  ✓ db.php 存在\n";
    require_once $dbPath;
    echo "  ✓ db.php 已加载\n";
} else {
    echo "  ✗ db.php 不存在\n";
}

echo "\n3. 测试 db() 函数:\n";
if (function_exists('db')) {
    try {
        $conn = db();
        if ($conn) {
            echo "  ✓ db() 返回连接对象\n";
            echo "  - 类: " . get_class($conn) . "\n";
            
            // 测试查询
            $count = $conn->query("SELECT COUNT(*) FROM tools WHERE status=1")->fetchColumn();
            echo "  - 工具数: $count\n";
        } else {
            echo "  ✗ db() 返回 NULL\n";
        }
    } catch (Exception $e) {
        echo "  ✗ 异常: " . $e->getMessage() . "\n";
    }
} else {
    echo "  ✗ db() 函数不存在\n";
}

echo "\n=== 调试结束 ===\n";