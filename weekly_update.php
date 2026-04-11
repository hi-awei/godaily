<?php
/**
 * GoDaily 周刊自动更新脚本
 * 触发URL: https://993899.com/weekly_update.php?key=godaily2026
 * 
 * 功能：
 * 1. 更新sitemap
 * 2. 推送到搜索引擎
 * 3. 清理缓存
 * 4. 记录更新日志
 */

// 安全验证
$validKey = 'godaily2026';
$providedKey = $_GET['key'] ?? '';

if ($providedKey !== $validKey) {
    http_response_code(403);
    die('Access denied');
}

header('Content-Type: text/plain; charset=utf-8');
echo "=== GoDaily 周刊自动更新 " . date('Y-m-d H:i:s') . " ===\n\n";

require_once 'config.php';

$db = getDb();
if (!$db) {
    die("数据库连接失败\n");
}

// 1. 更新工具统计
echo "[1/5] 更新工具统计...\n";
$result = $db->query("SELECT COUNT(*) as total, 
    SUM(CASE WHEN status='published' THEN 1 ELSE 0 END) as published,
    SUM(CASE WHEN DATE(created_at)=CURDATE() THEN 1 ELSE 0 END) as today_new
    FROM tools");
$stats = $result->fetch_assoc();
echo "  - 总工具数: {$stats['total']}\n";
echo "  - 已发布: {$stats['published']}\n";
echo "  - 今日新增: {$stats['today_new']}\n\n";

// 2. 更新资讯统计
echo "[2/5] 更新资讯统计...\n";
$result = $db->query("SELECT COUNT(*) as total FROM news");
$newsCount = $result->fetch_assoc()['total'];
echo "  - 资讯总数: {$newsCount}\n\n";

// 3. 更新热门工具浏览排行
echo "[3/5] 更新热门排行...\n";
$hotTools = $db->query("SELECT id, name, view_count FROM tools WHERE status='published' ORDER BY view_count DESC LIMIT 10");
$rank = 1;
echo "  TOP 10:\n";
while ($t = $hotTools->fetch_assoc()) {
    echo "  {$rank}. {$t['name']} - {$t['view_count']} 浏览\n";
    $rank++;
}
echo "\n";

// 4. 推送sitemap到搜索引擎
echo "[4/5] 推送搜索引擎...\n";
$sitemapUrl = 'https://www.993899.com/sitemap.xml';

// 百度推送
$bdResult = @file_get_contents('http://data.zz.baidu.com/urls?site=https://www.993899.com&token=your_baidu_token', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: text/plain',
        'content' => $sitemapUrl
    ]
]));
echo "  - 百度推送: " . ($bdResult ? $bdResult : '跳过（未配置token）') . "\n";

// Bing推送
$bingResult = @file_get_contents('https://ssl.bing.com/webmaster/api.svc/json/SubmitUrl?apikey=your_bing_key&siteUrl=https://www.993899.com&url=' . urlencode($sitemapUrl));
echo "  - Bing推送: " . ($bingResult ? '已提交' : '跳过（未配置key）') . "\n\n";

// 5. 记录更新日志
echo "[5/5] 记录更新日志...\n";
$logFile = __DIR__ . '/weekly_update.log';
$logEntry = date('Y-m-d H:i:s') . " | 工具:{$stats['published']} | 资讯:{$newsCount} | 今日新增:{$stats['today_new']}\n";
file_put_contents($logFile, $logEntry, FILE_APPEND);
echo "  - 日志已写入: weekly_update.log\n\n";

// 清理临时文件
$tempFiles = glob(__DIR__ . '/tmp_*');
foreach ($tempFiles as $f) {
    if (is_file($f) && filemtime($f) < time() - 86400) {
        unlink($f);
    }
}

echo "=== 更新完成 ===\n";
echo "周刊地址: https://www.993899.com/weekly.php\n";
echo "完成时间: " . date('Y-m-d H:i:s') . "\n";
