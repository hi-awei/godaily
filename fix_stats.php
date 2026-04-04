<?php
// 快速更新浏览量和投票数
$host = '127.0.0.1';
$dbname = 'web01_com';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    $tools = $db->query("SELECT id FROM tools")->fetchAll();
    foreach ($tools as $t) {
        $views = rand(50, 500);
        $votes = rand(0, 15);
        $db->prepare("UPDATE tools SET views=?, vote_count=? WHERE id=?")->execute([$views, $votes, $t['id']]);
    }
    
    $news = $db->query("SELECT id FROM news")->fetchAll();
    foreach ($news as $n) {
        $views = rand(20, 200);
        $db->prepare("UPDATE news SET view_count=? WHERE id=?")->execute([$views, $n['id']]);
    }
    
    echo "done: " . count($tools) . " tools, " . count($news) . " news";
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}