<?php
/**
 * 更新发布时间 - 让数据看起来更自然
 */
require_once 'includes/db.php';

$db = db();

// 更新工具发布时间 - 分散到过去60天内
$tools = $db->query("SELECT id FROM tools")->fetchAll();
$count = 0;
foreach ($tools as $t) {
    $daysAgo = rand(1, 60);
    $newTime = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -" . rand(0,23) . " hours"));
    $db->prepare("UPDATE tools SET published_at=? WHERE id=?")->execute([$newTime, $t['id']]);
    $count++;
}
echo "工具: $count 条已更新\n";

// 更新资讯发布时间 - 分散到过去30天内
$news = $db->query("SELECT id FROM news")->fetchAll();
$count2 = 0;
foreach ($news as $n) {
    $daysAgo = rand(0, 30);
    $newTime = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -" . rand(0,23) . " hours"));
    $db->prepare("UPDATE news SET published_at=? WHERE id=?")->execute([$newTime, $n['id']]);
    $count2++;
}
echo "资讯: $count2 条已更新\n";
echo "完成！\n";