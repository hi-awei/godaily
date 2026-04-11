<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: text/plain; charset=utf-8');

// Simple SQL executor for fix_icons.sql
$key = isset($_GET['key']) ? $_GET['key'] : '';
if ($key !== 'godaily2026') { die('Unauthorized'); }

require_once 'includes/db.php';
$db = db();

$file = __DIR__ . '/fix_icons.sql';
if (!file_exists($file)) { die('SQL file not found'); }

$sql = file_get_contents($file);
$stmts = array_filter(array_map('trim', explode(';', $sql)), fn($s) => $s && !str_starts_with($s, '--'));

$updated = 0;
foreach ($stmts as $stmt) {
    if (!$stmt) continue;
    try {
        $db->exec($stmt);
        $updated++;
        echo "OK: " . substr($stmt, 0, 60) . "\n";
    } catch (Exception $e) {
        echo "FAIL: " . substr($stmt, 0, 60) . " → " . $e->getMessage() . "\n";
    }
}
echo "\nTotal: $updated statements executed";
?>
