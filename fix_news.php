<?php
// 更新资讯时间
$host = '127.0.0.1';
$dbname = 'web01_com';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    $news = $db->query("SELECT id, title FROM news")->fetchAll();
    foreach ($news as $n) {
        $daysAgo = rand(1, 20);
        $hours = rand(0, 23);
        $ts = time() - ($daysAgo * 86400 + $hours * 3600);
        $newTime = date('Y-m-d H:i:s', $ts);
        
        $summary = "关于" . mb_substr($n['title'], 0, mb_strlen($n['title'])-4) . "的最新报道。该消息在业内引发广泛关注，分析认为这将对该领域产生深远影响。";
        
        $stmt = $db->prepare("UPDATE news SET published_at=?, summary=? WHERE id=?");
        $stmt->execute([$newTime, $summary, $n['id']]);
    }
    echo "news: " . count($news) . " updated\n";
    echo "done";
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}