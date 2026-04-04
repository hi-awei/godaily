<?php
// test_write.php - 测试 admin 目录写入权限
if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') { http_response_code(403); exit; }
$testFile = __DIR__ . '/admin/test_write.txt';
$ok = file_put_contents($testFile, 'test');
if ($ok !== false) {
    echo "WRITE OK: admin/ is writable";
    @unlink($testFile);
} else {
    echo "WRITE FAILED: admin/ is NOT writable";
}
