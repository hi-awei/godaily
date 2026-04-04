<?php
/**
 * 百度搜索推送API
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$PUSH_KEY = 'godaily2026';
$BAIDU_TOKEN = 'TDOFRVv0z1ajN9GA';
$BAIDU_SITE = 'https://www.993899.com';
$BAIDU_API = "http://data.zz.baidu.com/urls?site={$BAIDU_SITE}&token={$BAIDU_TOKEN}";

if (empty($_GET['key']) || $_GET['key'] !== $PUSH_KEY) {
    die('Access denied.');
}

echo "=== 百度链接推送 ===\n";
echo "目标: {$BAIDU_SITE}\n\n";
flush();

// 收集链接
$urls = [];
$urls[] = $BAIDU_SITE . '/';
$urls[] = $BAIDU_SITE . '/tools.php';
$urls[] = $BAIDU_SITE . '/news.php';
$urls[] = $BAIDU_SITE . '/privacy.php';

try {
    $db = db();
    $rows = $db->query("SELECT id FROM tools WHERE status=1")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($rows as $id) {
        $urls[] = $BAIDU_SITE . '/tool.php?id=' . $id;
    }
    
    $cats = $db->query("SELECT DISTINCT slug FROM tool_categories WHERE slug IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cats as $slug) {
        $urls[] = $BAIDU_SITE . '/tools.php?category=' . $slug;
    }
    
    $news = $db->query("SELECT id FROM news WHERE status=1")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($news as $id) {
        $urls[] = $BAIDU_SITE . '/news.php?id=' . $id;
    }
} catch (Exception $e) {
    echo "数据库错误: " . $e->getMessage() . "\n";
}

$total = count($urls);
echo "共收集 {$total} 个链接\n";
echo "正在推送...\n";
flush();

// 推送
$postData = implode("\n", $urls);

// 方法1: curl
if (function_exists('curl_init')) {
    echo "使用 curl 推送...\n";
    flush();
    $ch = curl_init($BAIDU_API);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP: {$code}\n";
    if ($err) echo "curl错误: {$err}\n";
    if ($result) echo "响应: {$result}\n";
    
// 方法2: file_get_contents
} else {
    echo "使用 file_get_contents 推送...\n";
    flush();
    $ctx = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => $postData,
        'timeout' => 30,
    ]]);
    $result = @file_get_contents($BAIDU_API, false, $ctx);
    echo "响应: {$result}\n";
}

echo "\n=== 完成 ===\n";