<?php
require_once 'includes/db.php';
$db = db();

// 直接更新所有工具发布时间为过去30天内随机
$tools = $db->query("SELECT id FROM tools")->fetchAll();
foreach ($tools as $i => $t) {
    $days = rand(3, 30);
    $hours = rand(0, 23);
    $time = date('Y-m-d H:i:s', strtotime("-{$days} days -{$hours} hours"));
    $db->prepare("UPDATE tools SET published_at=? WHERE id=?")->execute([$time, $t['id']]);
}
echo "tools: " . count($tools) . "\n";

// 资讯
$news = $db->query("SELECT id FROM news")->fetchAll();
foreach ($news as $n) {
    $days = rand(1, 14);
    $hours = rand(0, 23);
    $time = date('Y-m-d H:i:s', strtotime("-{$days} days -{$hours} hours"));
    $db->prepare("UPDATE news SET published_at=? WHERE id=?")->execute([$time, $n['id']]);
}
echo "news: " . count($news) . "\ndone";