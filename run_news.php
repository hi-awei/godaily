<?php
// run_news.php - 采集资讯并写入日志
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

function parseRssFeed($url) {
    $xml = @file_get_contents($url);
    if (!$xml) return [];
    libxml_use_internal_errors(true);
    $doc = @simplexml_load_string($xml);
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

$sources = [
    '机器之心' => 'https://www.jiqizhixin.com/rss',
    '量子位' => 'https://www.qbitai.com/feed',
];

$aiKeywords = ['AI','GPT','ChatGPT','Claude','大模型','人工智能','机器学习','深度学习','LLM','AIGC','Midjourney','Stable Diffusion','OpenAI','Anthropic','Google AI','文心','通义','智谱','Gemini','Sora','o1','RAG','Agent','扩散模型','神经网络','AI助手','Copilot','文生图','视频生成'];

$log = [];
$log[] = "开始采集: " . date('Y-m-d H:i:s');
$added = 0;

try {
    $db = db();
    foreach ($sources as $src => $url) {
        $log[] = "抓取: $src";
        $items = parseRssFeed($url);
        $log[] = "  获取到 " . count($items) . " 条";
        foreach ($items as $item) {
            $exists = $db->query("SELECT id FROM news WHERE url=" . $db->quote($item['link']))->fetch();
            if ($exists) { $log[] = "  跳过(已存在): " . mb_substr($item['title'],0,30); continue; }
            $text = $item['title'] . ' ' . $item['description'];
            $isAi = false;
            foreach ($aiKeywords as $kw) { if (stripos($text, $kw) !== false) { $isAi = true; break; } }
            if (!$isAi) { $log[] = "  跳过(非AI): " . mb_substr($item['title'],0,30); continue; }
            $ts = @strtotime($item['pubDate']) ?: time();
            $db->prepare("INSERT INTO news (title,summary,url,source,published_at,status,created_at) VALUES (?,?,?,?,?,1,NOW())")->execute([
                mb_substr($item['title'],0,200),
                mb_substr(strip_tags($item['description']),0,300),
                $item['link'], $src, date('Y-m-d H:i:s', $ts)
            ]);
            $added++;
            $log[] = "  +入库: " . mb_substr($item['title'],0,40);
        }
        sleep(2);
    }
} catch (Exception $e) {
    $log[] = "ERROR: " . $e->getMessage();
}

$log[] = "总计新增: $added 条";
$log[] = "完成: " . date('Y-m-d H:i:s');

$output = implode("\n", $log);
file_put_contents(__DIR__ . '/news_collect_log.txt', $output);
echo $output;
