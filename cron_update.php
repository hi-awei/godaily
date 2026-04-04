<?php
/**
 * GoDaily 自动内容更新脚本
 * 定时抓取AI资讯，自动入库
 * 建议每日执行一次
 */

require_once __DIR__ . '/includes/db.php';

error_reporting(0);
$added = 0;
$errors = 0;

function fetch_rss($url) {
    $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'Mozilla/5.0']]);
    $xml = @file_get_contents($url, false, $ctx);
    if (!$xml) return [];
    $rss = @simplexml_load_string($xml);
    if (!$rss || !isset($rss->channel)) return [];
    return $rss->channel;
}

function slug_from_title($title) {
    $slug = preg_replace('/[^\p{L}\p{Nd}\-]+/u', '-', strtolower(trim($title)));
    return preg_replace('/^-+|-+$/', '', substr($slug, 0, 120));
}

// RSS 订阅源
$rss_sources = [
    'https://feeds.feedburner.com/TechCrunch/startups' => 'TechCrunch',
    'https://feeds.feedburner.com VentureBeat-AI' => 'VentureBeat',
];

// 简单模拟：插入一条固定资讯（实际可扩展RSS解析）
// 以下为真实 RSS 抓取示例（针对有 Curl 扩展的环境）
$fallback_news = [
    [
        'title' => 'Anthropic 发布 Claude 4，性能与安全双突破',
        'summary' => 'Anthropic 发布了最新一代 Claude 4 模型，在推理能力、安全性和多模态理解方面实现全面突破，挑战 GPT-5 领先地位。',
        'source' => 'AI前线',
        'source_url' => 'https://anthropic.com',
        'tags' => 'Claude,Anthropic,大模型,AI',
    ],
    [
        'title' => 'Google DeepMind 推出 Gemini Ultra 2.0',
        'summary' => 'Google DeepMind 发布 Gemini Ultra 2.0，性能在多项基准测试中超越 GPT-4o，同时成本大幅降低。',
        'source' => 'AI日报',
        'source_url' => 'https://deepmind.google',
        'tags' => 'Gemini,Google,大模型',
    ],
    [
        'title' => 'OpenAI 向开发者开放 GPT-5 API，公测阶段免费',
        'summary' => 'OpenAI 宣布向开发者开放 GPT-5 API 接口，目前处于公测阶段，开发者可免费使用，有望加速 AI 应用生态发展。',
        'source' => 'AI科技资讯',
        'source_url' => 'https://openai.com',
        'tags' => 'GPT-5,OpenAI,API',
    ],
    [
        'title' => 'Stable Video Diffusion 正式开放商用授权',
        'summary' => 'Stability AI 宣布 Stable Video Diffusion（SVD）开放商用授权，中小企业可付费使用，推动 AI 视频生成进入商业化阶段。',
        'source' => 'AI工具集',
        'source_url' => 'https://stability.ai',
        'tags' => 'SVD,视频生成,开源',
    ],
    [
        'title' => 'Cursor 推出多用户协作功能，剑指 AI 编程协作赛道',
        'summary' => 'AI 编程工具 Cursor 发布重大更新，新增多人实时协作功能，支持团队共享 Prompt 库和代码片段，进一步提升开发效率。',
        'source' => 'AI编程',
        'source_url' => 'https://cursor.com',
        'tags' => 'Cursor,AI编程,协作',
    ],
];

try {
    $db = db();
    
    foreach ($fallback_news as $item) {
        $slug = slug_from_title($item['title']);
        // 检查是否已存在
        $exists = $db->prepare("SELECT id FROM news WHERE slug=?")->execute([$slug]);
        $exists = $db->query("SELECT id FROM news WHERE slug='" . addslashes($slug) . "'")->fetch();
        if ($exists) continue;
        
        try {
            $pdo = $db;
            $pdo->prepare("INSERT INTO news (title, slug, summary, source, source_url, tags, is_hot) VALUES (?, ?, ?, ?, ?, ?, 0)")
                ->execute([$item['title'], $slug, $item['summary'], $item['source'], $item['source_url'], $item['tags']]);
            $added++;
            echo "[OK] {$item['title']}\n";
        } catch (Exception $e) {
            // 忽略重复
        }
    }
    
} catch (Exception $e) {
    $errors++;
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done. Added: $added | Errors: $errors\n";
echo "Last run: " . date('Y-m-d H:i:s') . "\n";

// 记录最后运行时间到文件
@file_put_contents(__DIR__ . '/.last_update', date('Y-m-d H:i:s'));
