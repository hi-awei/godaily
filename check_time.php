<?php
require_once 'includes/db.php';
$db = db();

// 查看当前时间分布
$tools = $db->query("SELECT id, name, published_at FROM tools ORDER BY id LIMIT 10")->fetchAll();
foreach ($tools as $t) {
    echo $t['id'] . " | " . substr($t['published_at'], 0, 16) . " | " . $t['name'] . "\n";
}
echo "\n---\n";
$news = $db->query("SELECT id, title, published_at FROM news ORDER BY id LIMIT 5")->fetchAll();
foreach ($news as $n) {
    echo $n['id'] . " | " . substr($n['published_at'], 0, 16) . " | " . mb_substr($n['title'], 0, 20) . "\n";
}