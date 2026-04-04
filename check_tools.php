<?php
// check.php - 返回工具总数
error_reporting(0);
ini_set('display_errors', 0);
ini_set('zlib.output_compression', '0');
// Returns simple number, no Chinese chars
$db = @new mysqli('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com');
if ($db->connect_error) { echo "DBERR"; exit; }
$db->set_charset('utf8mb4');
$count = $db->query("SELECT COUNT(*) as c FROM tools")->fetch()['c'];
$pending = $db->query("SELECT COUNT(*) as c FROM submissions WHERE status=0")->fetch()['c'];
echo "T:" . $count . ",P:" . $pending;
$db->close();
