<?php
// exec_sql.php - 执行SQL文件
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', 0);
$sqlFile = __DIR__ . '/news_import.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found: $sqlFile\n";
    exit;
}
$sql = file_get_contents($sqlFile);
$lines = preg_split('/;\s*\n/', $sql);
$total = 0;
$ok = 0;
require_once __DIR__ . '/includes/db.php';
try {
    $db = db();
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '--') === 0) continue;
        if (stripos($line, 'SET NAMES') !== false) continue;
        $db->exec($line);
        $ok++;
        $total++;
        if ($total % 20 === 0) { echo "."; }
    }
    echo "\nDone: $ok statements executed. Total rows affected.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Failed at statement $ok\n";
}
