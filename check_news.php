<?php
// check_news.php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
$db = db();
$cols = $db->query("SHOW COLUMNS FROM news")->fetchAll();
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . "\n";
echo "\nCount: " . $db->query("SELECT COUNT(*) FROM news")->fetchColumn() . "\n";
$row = $db->query("SELECT * FROM news LIMIT 1")->fetch();
if ($row) { echo "\nSample:\n"; foreach ($row as $k => $v) echo "  $k = " . substr($v,0,80) . "\n"; }
