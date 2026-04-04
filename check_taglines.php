<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
$db = db();

$total = $db->query("SELECT COUNT(*) FROM tools")->fetchColumn();
$withTagline = $db->query("SELECT COUNT(*) FROM tools WHERE tagline IS NOT NULL AND tagline != ''")->fetchColumn();
$withoutTagline = $total - $withTagline;

echo "Total tools: $total\n";
echo "With tagline: $withTagline\n";
echo "Without tagline: $withoutTagline\n";

echo "\n--- Sample tools WITH tagline ---\n";
$rows = $db->query("SELECT name, tagline, category FROM tools WHERE tagline != '' AND tagline IS NOT NULL LIMIT 20")->fetchAll();
foreach ($rows as $r) {
    echo "{$r['name']} | {$r['tagline']} | {$r['category']}\n";
}

echo "\n--- Sample tools WITHOUT tagline (first 10) ---\n";
$rows = $db->query("SELECT name, description, category FROM tools WHERE (tagline IS NULL OR tagline = '') LIMIT 10")->fetchAll();
foreach ($rows as $r) {
    $desc = mb_substr(strip_tags($r['description'] ?? ''), 0, 80);
    echo "{$r['name']} | {$desc} | {$r['category']}\n";
}
