<?php
/**
 * RSS Feed 生成器
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/rss+xml; charset=utf-8');

$siteUrl = rtrim(SITE_URL, '/');
$siteName = SITE_NAME;
$siteDesc = SITE_DESC;

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;
echo '<channel>' . PHP_EOL;
echo "<title>$siteName</title>" . PHP_EOL;
echo "<link>$siteUrl</link>" . PHP_EOL;
echo "<description>$siteDesc</description>" . PHP_EOL;
echo "<language>zh-CN</language>" . PHP_EOL;
echo "<atom:link href=\"$siteUrl/rss.php\" rel=\"self\" type=\"application/rss+xml\"/>" . PHP_EOL;

try {
    $db = db();
    
    // 最新工具
    $tools = $db->query("SELECT * FROM tools WHERE status=1 ORDER BY created_at DESC LIMIT 20")->fetchAll();
    foreach ($tools as $t) {
        $title = clean($t['name']);
        $link = "$siteUrl/tool.php?slug=" . $t['slug'];
        $desc = clean($t['tagline'] ?: $t['description']);
        $pubDate = date('r', strtotime($t['created_at']));
        
        echo '<item>' . PHP_EOL;
        echo "<title>$title - AI工具</title>" . PHP_EOL;
        echo "<link>$link</link>" . PHP_EOL;
        echo "<description>$desc</description>" . PHP_EOL;
        echo "<pubDate>$pubDate</pubDate>" . PHP_EOL;
        echo "<guid isPermaLink=\"true\">$link</guid>" . PHP_EOL;
        echo '</item>' . PHP_EOL;
    }
    
    // 最新资讯
    $news = $db->query("SELECT * FROM news WHERE status=1 ORDER BY published_at DESC LIMIT 10")->fetchAll();
    foreach ($news as $n) {
        $title = clean($n['title']);
        $link = "$siteUrl/news_detail.php?id=" . $n['id'];
        $desc = clean($n['summary'] ?: mb_substr(strip_tags($n['content'] ?? ''), 0, 200));
        $pubDate = date('r', strtotime($n['published_at']));
        
        echo '<item>' . PHP_EOL;
        echo "<title>$title - AI资讯</title>" . PHP_EOL;
        echo "<link>$link</link>" . PHP_EOL;
        echo "<description>$desc</description>" . PHP_EOL;
        echo "<pubDate>$pubDate</pubDate>" . PHP_EOL;
        echo "<guid isPermaLink=\"true\">$link</guid>" . PHP_EOL;
        echo '</item>' . PHP_EOL;
    }
} catch (Exception $e) {
    // ignore
}

echo '</channel>' . PHP_EOL;
echo '</rss>';
