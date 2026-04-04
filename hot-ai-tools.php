<?php
/**
 * AI工具周刊页 - 每周精选
 */
$pageTitle = 'AI工具周刊 - GoDaily';
$pageDesc = '每周精选最值得关注的AI工具和技术动态，助你紧跟AI发展趋势。';

$weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));

require_once 'includes/db.php';
require_once 'includes/functions.php';

$db = db();
$weeklyTools = $db->query("SELECT * FROM tools WHERE status=1 AND created_at >= '$weekAgo' ORDER BY created_at DESC LIMIT 30")->fetchAll();
$allTools = $db->query("SELECT * FROM tools WHERE status=1 ORDER BY created_at DESC LIMIT 50")->fetchAll();

$cateMap = [
    'chatbot' => 'AI对话', 'image' => 'AI绘画', 'video' => 'AI视频',
    'audio' => 'AI音频', 'office' => 'AI办公', 'productivity' => '效率工具',
    'developer' => '编程助手', 'search' => 'AI搜索', 'api' => 'AI大模型',
    'design' => '设计工具', 'writing' => '写作助手', 'education' => '学习教育',
    'detector' => 'AI检测', 'other' => '其他工具',
];
$pricingMap = ['free'=>'免费', 'freemium'=>'免费+付费', 'paid'=>'付费', 'contact'=>'联系定价'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDesc ?>">
    <link rel="canonical" href="https://993899.com/weekly.php">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, "PingFang SC", "Microsoft YaHei", sans-serif; background: #f8fafc; color: #334155; }
        header { background: linear-gradient(135deg, #0f172a, #1e3a5f); padding: 60px 20px 40px; text-align: center; color: white; }
        header h1 { font-size: 32px; margin-bottom: 12px; }
        header p { opacity: 0.8; font-size: 15px; }
        .week-badge { display: inline-block; background: #fbbf24; color: #000; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
        .section-title { font-size: 18px; margin-bottom: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: 0.2s; cursor: pointer; text-decoration: none; color: inherit; display: block; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
        .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .card-icon { width: 44px; height: 44px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; overflow: hidden; flex-shrink: 0; }
        .card-icon img { width: 100%; height: 100%; object-fit: cover; }
        .card-title { font-size: 15px; font-weight: 600; color: #1e293b; }
        .card-title small { display: block; font-size: 12px; color: #64748b; font-weight: 400; }
        .card-tagline { font-size: 13px; color: #475569; margin-bottom: 8px; }
        .card-desc { font-size: 12px; color: #94a3b8; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 10px; border-top: 1px solid #f1f5f9; }
        .badge { padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-free { background: #dcfce7; color: #16a34a; }
        .badge-freemium { background: #fef9c3; color: #ca8a04; }
        .badge-paid { background: #fee2e2; color: #dc2626; }
        .cat-tag { font-size: 12px; color: #94a3b8; }
        .back { display: inline-block; margin-bottom: 20px; color: #6366f1; text-decoration: none; font-size: 14px; }
        footer { text-align: center; padding: 24px; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; margin-top: 40px; }
        footer a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <div class="week-badge">📅 本周新增</div>
        <h1>🔥 AI 工具周刊</h1>
        <p>每周精选新收录的 AI 工具，紧跟 AI 发展趋势</p>
    </header>
    
    <div class="container">
        <a href="/" class="back">← 返回首页</a>
        
        <?php if (!empty($weeklyTools)): ?>
            <div class="section-title">✨ 本周新增工具 (<?= count($weeklyTools) ?> 个)</div>
            <div class="grid">
                <?php foreach ($weeklyTools as $tool): ?>
                    <a href="/tool.php?slug=<?= urlencode($tool['slug']) ?>" class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <?php if (!empty($tool['icon']) && strpos($tool['icon'], 'http') === 0): ?>
                                    <img src="<?= clean($tool['icon']) ?>" alt="<?= clean($tool['name']) ?>" loading="lazy">
                                <?php else: ?>
                                    🛠️
                                <?php endif; ?>
                            </div>
                            <div class="card-title">
                                <?= clean($tool['name']) ?>
                                <small><?= $cateMap[$tool['category']] ?? '' ?></small>
                            </div>
                        </div>
                        <div class="card-tagline"><?= clean($tool['tagline'] ?? '') ?></div>
                        <div class="card-desc"><?= clean($tool['description'] ?? '') ?></div>
                        <div class="card-footer">
                            <span class="badge badge-<?= $tool['pricing'] ?? 'free' ?>"><?= $pricingMap[$tool['pricing']] ?? '免费' ?></span>
                            <span class="cat-tag"><?= date('m-d', strtotime($tool['created_at'])) ?> 加入</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="section-title">📦 最近收录 (共 <?= count($allTools) ?> 个)</div>
        <div class="grid">
            <?php foreach (array_slice($allTools, 0, 20) as $tool): ?>
                <a href="/tool.php?slug=<?= urlencode($tool['slug']) ?>" class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <?php if (!empty($tool['icon']) && strpos($tool['icon'], 'http') === 0): ?>
                                <img src="<?= clean($tool['icon']) ?>" alt="<?= clean($tool['name']) ?>" loading="lazy">
                            <?php else: ?>
                                🛠️
                            <?php endif; ?>
                        </div>
                        <div class="card-title">
                            <?= clean($tool['name']) ?>
                            <small><?= $cateMap[$tool['category']] ?? '' ?></small>
                        </div>
                    </div>
                    <div class="card-tagline"><?= clean($tool['tagline'] ?? '') ?></div>
                    <div class="card-footer">
                        <span class="badge badge-<?= $tool['pricing'] ?? 'free' ?>"><?= $pricingMap[$tool['pricing']] ?? '免费' ?></span>
                        <span class="cat-tag"><?= $cateMap[$tool['category']] ?? '' ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <footer>
        由 <a href="https://993899.com">GoDaily AI工具导航</a> 整理 · <a href="/rss.php">RSS订阅</a>
    </footer>
</body>
</html>
