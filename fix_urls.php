<?php
// 检查工具URL状态并随机更新浏览量投票数
$host = '127.0.0.1';
$dbname = 'web01_com';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    $tools = $db->query("SELECT id, name, url FROM tools")->fetchAll();
    $results = [];
    $badUrls = [];
    
    foreach ($tools as $t) {
        // 检查URL状态
        $status = checkUrl($t['url']);
        $results[] = $t['name'] . ' => ' . $status;
        
        if (strpos($status, '200') === false) {
            $badUrls[] = $t['id'] . ',' . $t['name'] . ',' . $t['url'] . ',' . $status;
        }
        
        // 随机生成浏览量(50-500)和投票数(0-15)
        $views = rand(50, 500);
        $votes = rand(0, 15);
        $stmt = $db->prepare("UPDATE tools SET views=?, vote_count=? WHERE id=?");
        $stmt->execute([$views, $votes, $t['id']]);
    }
    
    echo "URL检查结果:\n";
    foreach ($results as $r) echo $r . "\n";
    
    echo "\n坏掉的URL:\n";
    foreach ($badUrls as $b) echo $b . "\n";
    
    echo "\n更新完成";
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}

function checkUrl($url) {
    if (!$url) return 'no url';
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_NOBODY => true
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    return $code ?: $error;
}
