<?php
// download.php - 提供 SQL 文件下载
if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') { http_response_code(403); exit; }
$sqlFile = __DIR__ . '/aidh_import.sql';
if (!file_exists($sqlFile)) { echo "SQL file not found"; exit; }
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="aidh_import.sql"');
header('Content-Length: ' . filesize($sqlFile));
readfile($sqlFile);
