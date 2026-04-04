<?php
// collector.php - 采集资讯并写入日志
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

function parseRssFeed($url) {
    $xml = @file_get_contents($url);
    if (!$xml) return [];
    // 如果返回HTML而非XML，跳过
    if (strpos(trim($xml), '<') !== 0) return [];
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

// AI相关RSS源
$sources = [
    '量子位' => 'https://www.qbitai.com/feed',
    '新智元' => 'https://zhidx.com/feed',
    'DeepMind Blog' => 'https://deepmind.google/blog/rss.xml',
    'Hugging Face' => 'https://huggingface.co/blog/feed.xml',
    'VentureBeat AI' => 'https://venturebeat.com/category/ai/feed/',
    'The Verge AI' => 'https://www.theverge.com/rss/ai-artificial-intelligence/index.xml',
    'MIT Tech Review' => 'https://www.technologyreview.com/feed/',
    'ArXiv cs.AI' => 'https://rss.arxiv.org/rss/cs.AI',
];

$aiKeywords = [
    'AI', 'GPT', 'ChatGPT', 'Claude', 'Gemini', 'Llama', 'Sora', 'o1', 'o3',
    '大模型', '人工智能', 'AIGC', 'LLM', '机器学习', '深度学习',
    'Midjourney', 'Stable Diffusion', 'OpenAI', 'Anthropic', 'Google AI',
    '文心', '通义', '智谱', 'Kimi', '豆包', 'Copilot', 'Copilot',
    'Agent', 'RAG', 'RAG', 'Agent', 'Diffusion', '神经网络',
    'Stable Diffusion', '生成式AI', 'Generative AI', 'AI Agent',
    'LLaVA', 'Mistral', 'Gemma', 'Qwen', 'MoE', 'Embedding'
];

$log = [];
$log[] = "开始采集: " . date('Y-m-d H:i:s');
$added = 0;
$totalFetched = 0;

try {
    $db = db();
    foreach ($sources as $src => $url) {
        $items = parseRssFeed($url);
        $totalFetched += count($items);
        $log[] = "抓取: $src => " . count($items) . " 条";
        
        foreach ($items as $item) {
            $exists = $db->query("SELECT id FROM news WHERE url=" . $db->quote($item['link']))->fetch();
            if ($exists) continue;
            
            $text = $item['title'] . ' ' . $item['description'];
            $isAi = false;
            foreach ($aiKeywords as $kw) {
                if (stripos($text, $kw) !== false) { $isAi = true; break; }
            }
            if (!$isAi) continue;
            
            $ts = @strtotime($item['pubDate']) ?: time();
            $db->prepare("INSERT INTO news (title,summary,url,source,published_at,status,created_at) VALUES (?,?,?,?,?,1,NOW())")
              ->execute([
                  mb_substr($item['title'], 0, 200),
                  mb_substr(strip_tags($item['description']), 0, 300),
                  $item['link'],
                  $src,
                  date('Y-m-d H:i:s', $ts)
              ]);
            $added++;
            $log[] = "  +入库: " . mb_substr($item['title'], 0, 40);
        }
        sleep(2);
    }
} catch (Exception $e) {
    $log[] = "ERROR: " . $e->getMessage();
}

$log[] = "总计: 取到 $totalFetched 条，过滤后新增 $added 条";
$log[] = "完成: " . date('Y-m-d H:i:s');

$output = implode("\n", $log);
file_put_contents(__DIR__ . '/nc_log.txt', $output);
echo $output;
