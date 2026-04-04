<?php
error_reporting(E_ALL);
require_once __DIR__ . '/includes/db.php';
$db = db();

// 添加 is_hot 字段到 tools 表
try {
    $db->exec("ALTER TABLE tools ADD COLUMN is_hot TINYINT(1) DEFAULT 0 AFTER featured");
    echo "成功添加 is_hot 字段\n";
} catch (Exception $e) {
    echo "添加字段失败: " . $e->getMessage() . "\n";
}

// 添加 tags 字段到 tools 表
try {
    $db->exec("ALTER TABLE tools ADD COLUMN tags VARCHAR(255) DEFAULT '' AFTER description");
    echo "成功添加 tags 字段\n";
} catch (Exception $e) {
    echo "添加 tags 字段失败（可能已存在）: " . $e->getMessage() . "\n";
}

// 添加 logo_url 字段到 tools 表
try {
    $db->exec("ALTER TABLE tools ADD COLUMN logo_url VARCHAR(500) DEFAULT '' AFTER url");
    echo "成功添加 logo_url 字段\n";
} catch (Exception $e) {
    echo "添加 logo_url 字段失败（可能已存在）: " . $e->getMessage() . "\n";
}
?>
