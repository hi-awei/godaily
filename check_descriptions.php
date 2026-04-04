<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
$db = db();

// 占位符关键词
$placeholder = ['优秀的AI工具', '具有强大的功能', '良好的用户体验', '业界广受好评', '适合各类用户', '一款优秀', '功能强大', '体验良好'];

$all = $db->query("SELECT id, name, description, tagline FROM tools WHERE status=1")->fetchAll();
$total = count($all);
$hasPlaceholder = 0;
$noDesc = 0;
$samples = [];

foreach ($all as $t) {
    $desc = strip_tags($t['description'] ?? '');
    if (strlen($desc) < 20) { $noDesc++; continue; }
    $isPlaceholder = false;
    foreach ($placeholder as $p) {
        if (strpos($desc, $p) !== false) { $isPlaceholder = true; break; }
    }
    if ($isPlaceholder) {
        $hasPlaceholder++;
        if (count($samples) < 15) {
            $samples[] = "{$t['name']} | {$t['tagline']} | " . mb_substr($desc, 0, 60);
        }
    }
}

echo "Total: $total | Placeholder desc: $hasPlaceholder | Empty desc: $noDesc\n\n";
echo "Samples (first 15):\n";
foreach ($samples as $s) echo "$s\n";
