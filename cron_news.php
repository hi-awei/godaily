<?php
/**
 * 自动采集 AI 资讯脚本
 * 通过 cron 每小时执行一次
 * 
 * 使用方法：
 * 1. 手动执行：php cron_news.php
 * 2. 定时执行：在服务器添加 crontab: 0 * * * * php /path/to/cron_news.php
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// 简单的 RSS 解析（不依赖外部库）
function parseRssFeed($url) {
    $xml = @file_get_contents($url);
    if (!$xml) return [];
    
    libxml_use_internal_errors(true);
    $doc = simplexml_load_string($xml);
    if (!$doc) return [];
    
    $items = [];
    $channel = $doc->channel ?? $doc;
    
    foreach ($channel->item as $item) {
        $items[] = [
            'title' => (string)$item->title,
            'link' => (string)$item->link,
            'description' => (string)$item->description,
            'pubDate' => (string)$item->pubDate,
        ];
    }
    
    return $items;
}

// AI 相关 RSS 源
$sources = [
    'OpenAI Blog' => 'https://openai.com/blog/rss.xml',
    'Google AI Blog' => 'https://blog.google/technology/ai/rss/',
    'Microsoft AI Blog' => 'https://blogs.microsoft.com/ai/feed/',
    '机器之心' => 'https://www.jiqizhixin.com/rss',
    '量子位' => 'https://www.qbitai.com/feed',
    '新智元' => 'https://www.jiqizhixin.com/rss',
];

// AI 关键词过滤
$aiKeywords = [
    'AI', 'GPT', 'ChatGPT', 'Claude', '大模型', '人工智能', '机器学习', 
    '深度学习', 'LLM', 'AIGC', 'Midjourney', 'Stable Diffusion',
    'OpenAI', 'Anthropic', 'Google AI', '文心', '通义', '智谱'
];

function containsAiKeyword($text, $keywords) {
    foreach ($keywords as $kw) {
        if (stripos($text, $kw) !== false) return true;
    }
    return false;
}

try {
    $db = db();
    $added = 0;
    
    foreach ($sources as $sourceName => $feedUrl) {
        echo "采集: $sourceName\n";
        $items = parseRssFeed($feedUrl);
        
        foreach ($items as $item) {
            // 检查是否已存在
            $exists = $db->query("SELECT id FROM news WHERE url=" . $db->quote($item['link']))->fetch();
            if ($exists) continue;
            
            // 检查是否 AI 相关
            $text = $item['title'] . ' ' . $item['description'];
            if (!containsAiKeyword($text, $aiKeywords)) continue;
            
            // 插入数据库
            $stmt = $db->prepare("INSERT INTO news (title, summary, url, source, published_at, status, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
            $stmt->execute([
                $item['title'],
                mb_substr(strip_tags($item['description']), 0, 200),
                $item['link'],
                $sourceName,
                date('Y-m-d H:i:s', strtotime($item['pubDate']) ?: time())
            ]);
            $added++;
        }
        
        echo "  采集完成，新增 $added 条\n";
        sleep(1); // 避免请求过快
    }
    
    echo "\n总计新增 $added 条资讯\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
