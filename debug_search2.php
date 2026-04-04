<?php
require_once 'includes/db.php';
$db = db();

// Check if search would find it
$search = '腾讯元宝';
$stmt = $db->prepare("SELECT id, name FROM tools WHERE status=1 AND (name LIKE ? OR tagline LIKE ? OR description LIKE ?)");
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$results = $stmt->fetchAll();
echo "Search '$search' results: " . count($results) . "\n";
foreach ($results as $r) echo "  ID={$r['id']} {$r['name']}\n";

// Check if icon files exist
$iconDir = '/www/wwwroot/web01.com/assets/icons/';
$files = glob($iconDir . '*');
echo "\nFiles in icons dir: " . count($files) . "\n";
foreach (array_slice($files, 0, 3) as $f) echo "  " . basename($f) . "\n";

// Check specific files
$check = ['腾讯元宝.png', 'Cursor.png', 'doubao8.png'];
foreach ($check as $c) {
    $p = $iconDir . $c;
    echo "  $c: " . (file_exists($p) ? "EXISTS (" . filesize($p) . " bytes)" : "MISSING") . "\n";
}
