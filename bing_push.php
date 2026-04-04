<?php
/**
 * 必应 IndexNow 推送
 * 文档：https://www.indexnow.org/documentation
 * 
 * 使用方法：
 * 1. 生成 API Key 并创建验证文件
 * 2. 访问 https://993899.com/bing_push.php?key=YOUR_KEY
 */

require_once __DIR__ . '/includes/db.php';

// 配置
define('SITE_URL', 'https://993899.com');
define('INDEXNOW_KEY', ''); // 任意字符串，如：a1b2c3d4e5f6g7h8
define('PUSH_KEY', ''); // 自定义访问密钥

// 验证访问密钥
if (empty($_GET['key']) || $_GET['key'] !== PUSH_KEY) {
    http_response_code(403);
    die('Access denied');
}

if (empty(INDEXNOW_KEY)) {
    die('请先配置 IndexNow Key');
}

// 获取所有链接
$urls = [SITE_URL . '/', SITE_URL . '/tools.php', SITE_URL . '/news.php'];

if ($db) {
    $tools = $db->query("SELECT id FROM tools WHERE status = 1")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tools as $tid) {
        $urls[] = SITE_URL . '/tool.php?id=' . $tid;
    }
    
    $categories = $db->query("SELECT DISTINCT slug FROM tool_categories WHERE slug IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($categories as $cat) {
        $urls[] = SITE_URL . '/tools.php?category=' . $cat;
    }
    
    $news = $db->query("SELECT id FROM news WHERE status = 1")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($news as $nid) {
        $urls[] = SITE_URL . '/news.php?id=' . $nid;
    }
}

echo "准备推送 " . count($urls) . " 个链接到 IndexNow...\n\n";

// IndexNow API（支持 Bing、Yandex、Seznam、Naver）
$indexnowUrl = 'https://www.bing.com/indexnow?url=' . urlencode(implode(',', $urls)) . '&key=' . INDEXNOW_KEY;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $indexnowUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP 状态码: $httpCode\n";

if ($httpCode === 200) {
    echo "推送成功！\n";
} else {
    echo "响应: $result\n";
}

echo "\n提示: 你也可以手动提交 sitemap:\n";
echo "https://www.bing.com/webmasters/ping?sitemap=" . urlencode(SITE_URL . '/sitemap.php') . "\n";
