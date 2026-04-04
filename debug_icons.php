<?php
require_once 'includes/db.php';
$db = db();

// Check 腾讯元宝
$tool = $db->prepare("SELECT id, name, icon, status, category FROM tools WHERE name LIKE '%腾讯元宝%' LIMIT 1")->fetch();
echo "Tool: " . ($tool ? "{$tool['name']} | icon={$tool['icon']} | status={$tool['status']} | cat={$tool['category']}" : "NOT FOUND") . "\n";

// Check all tools with icon containing miguyu
$remote = $db->query("SELECT COUNT(*) FROM tools WHERE status=1 AND icon LIKE 'http%'")->fetchColumn();
$local = $db->query("SELECT COUNT(*) FROM tools WHERE status=1 AND icon LIKE 'assets/icons/%'")->fetchColumn();
$empty = $db->query("SELECT COUNT(*) FROM tools WHERE status=1 AND (icon IS NULL OR icon = '')")->fetchColumn();
echo "\nRemote icons (CDN): $remote\nLocal icons: $local\nNo icon: $empty\n";

// Sample of remote icons
$sample = $db->query("SELECT name, icon FROM tools WHERE status=1 AND icon LIKE 'http%' LIMIT 3")->fetchAll();
echo "\nSample remote icons:\n";
foreach ($sample as $s) { echo "  {$s['name']}: {$s['icon']}\n"; }
