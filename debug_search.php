<?php
require_once 'includes/db.php';
$db = db();

// Search broadly
$like = $db->prepare("SELECT id, name, slug, icon FROM tools WHERE name LIKE '%元宝%' OR name LIKE '%腾讯%'");
$like->execute();
$r = $like->fetchAll();
echo "Results for '元宝' or '腾讯': " . count($r) . "\n";
foreach ($r as $t) { echo "  ID={$t['id']} name={$t['name']} slug={$t['slug']} icon={$t['icon']}\n"; }

// Also check by slug
$slug = $db->prepare("SELECT id, name, slug, icon FROM tools WHERE slug LIKE '%yuanbao%' OR slug LIKE '%tencent%'");
$slug->execute();
$r2 = $slug->fetchAll();
echo "\nResults for slug 'yuanbao' or 'tencent': " . count($r2) . "\n";
foreach ($r2 as $t) { echo "  ID={$t['id']} name={$t['name']} slug={$t['slug']} icon={$t['icon']}\n"; }

// Show the category value for tencent
$catCheck = $db->query("SELECT id, name, category FROM tools WHERE category LIKE '%tencent%' OR category LIKE '%yuanbao%'")->fetchAll();
echo "\nCategory matches: " . count($catCheck) . "\n";
