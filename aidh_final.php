<?php
// aidh_final.php - MySQL charset 修复版
error_reporting(0);
ini_set('display_errors', 0);
ini_set('zlib.output_compression', '0');

if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') { http_response_code(403); exit; }

$db = @new mysqli('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com');
if ($db->connect_error) { echo "DBERR\n"; exit; }
// FIX: set charset BEFORE any queries
$db->set_charset('utf8mb4');
// Also send init command
$db->query("SET NAMES utf8mb4");

// Simple test: check if current tool count works
$result = $db->query("SELECT COUNT(*) as c FROM tools");
$row = $result->fetch_assoc();
echo "BEFORE:" . $row['c'] . "\n";
