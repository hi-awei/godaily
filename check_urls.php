<?php
// 检测所有工具URL状态
header('Content-Type: application/json');
require_once 'includes/db.php';
$db = db();
$tools = $db->query("SELECT id, name, url FROM tools WHERE status=1")->fetchAll();

$results = [];
foreach ($tools as $t) {
    $url = $t['url'];
    $status = 'unknown';
    $httpCode = 0;
    
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 400) {
            $status = 'ok';
        } elseif ($httpCode >= 400) {
            $status = 'error';
        }
    }
    
    $results[] = [
        'id' => $t['id'],
        'name' => $t['name'],
        'url' => $url,
        'http_code' => $httpCode,
        'status' => $status
    ];
}

echo json_encode(['total' => count($results), 'tools' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);