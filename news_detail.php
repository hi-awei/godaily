<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: news.php'); exit; }

try {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM news WHERE id=? AND status=1");
    $stmt->execute([$id]);
    $news = $stmt->fetch();
    if (!$news) { header('Location: news.php'); exit; }
    // Increment view count
    $db->prepare("UPDATE news SET view_count=view_count+1 WHERE id=?")->execute([$id]);
    
    // Get popular news
    $popular = $db->query("SELECT id, title, view_count FROM news WHERE status=1 ORDER BY view_count DESC LIMIT 5")->fetchAll();
    
    // Get recent news
    $recent = $db->query("SELECT id, title, published_at FROM news WHERE status=1 ORDER BY published_at DESC LIMIT 5")->fetchAll();
    
    // Get related tools based on title keywords
    $related_tools = [];
    $title_lower = mb_strtolower($news['title'], 'UTF-8');
    
    // Define keyword to tool mapping
    $keyword_map = [
        'kimi' => 'kimi',
        'midjourney' => 'midjourney',
        'cursor' => 'cursor',
        'chatgpt' => 'chatgpt',
        'claude' => 'claude',
        'gpt' => 'chatgpt',
        'stable diffusion' => 'stable-diffusion',
        'sd' => 'stable-diffusion',
        '文心一言' => 'wenxinyiyan',
        '通义' => 'tongyi',
        '豆包' => 'doubao',
        '秘塔' => 'misearch',
        'perplexity' => 'perplexity',
        'dall' => 'dalle',
        'suno' => 'suno',
        'runway' => 'runway',
        '视频' => 'ai-video',
        '图像' => 'ai-image',
        '绘画' => 'ai-image',
        '编程' => 'ai-code',
        '代码' => 'ai-code',
        '写作' => 'writing',
        '设计' => 'ai-design',
    ];
    
    $matched_slug = '';
    foreach ($keyword_map as $keyword => $slug) {
        if (strpos($title_lower, $keyword) !== false) {
            $matched_slug = $slug;
            break;
        }
    }
    
    if ($matched_slug) {
        // Get the tool itself
        $tool_stmt = $db->prepare("SELECT slug, name, tagline FROM tools WHERE slug=?");
        $tool_stmt->execute([$matched_slug]);
        $main_tool = $tool_stmt->fetch();
        
        // Get category for related tools
        if ($main_tool) {
            $cat_stmt = $db->prepare("SELECT category FROM tools WHERE slug=?");
            $cat_stmt->execute([$matched_slug]);
            $cat_row = $cat_stmt->fetch();
            if ($cat_row) {
                $related = $db->prepare("SELECT slug, name, tagline FROM tools WHERE category=? AND slug!=? LIMIT 4");
                $related->execute([$cat_row['category'], $matched_slug]);
                $related_tools = $related->fetchAll();
                if ($main_tool) array_unshift($related_tools, $main_tool);
            }
        }
    }
    
    // If no related tools found, show some popular tools
    if (empty($related_tools)) {
        $related_tools = $db->query("SELECT slug, name, tagline FROM tools ORDER BY view_count DESC LIMIT 5")->fetchAll();
    }
    
} catch (Exception $e) {
    header('Location: news.php'); exit;
}

$pageTitle = $news['title'];
$currentPage = 'news';

require_once 'templates/header.php';
?>

<div class="container">
    <div class="news-layout">
        <article class="news-detail">
            <div class="news-detail-header">
                <a href="news.php" class="back-link">← 返回资讯列表</a>
                <h1 class="news-detail-title"><?= clean($news['title']) ?></h1>
                <div class="news-detail-meta">
                    <span>📅 <?= date('Y-m-d', strtotime($news['published_at'])) ?></span>
                    <?php if ($news['source']): ?>
                        <span>📰 <?= clean($news['source']) ?></span>
                    <?php endif; ?>
                    <span>👁️ <?= number_format($news['view_count'] ?? 0) ?> 阅读</span>
                </div>
            </div>

            <?php if ($news['image']): ?>
                <div class="news-detail-image">
                    <img src="<?= clean($news['image']) ?>" alt="<?= clean($news['title']) ?>">
                </div>
            <?php endif; ?>

            <?php if ($news['content']): ?>
                <div class="news-detail-body">
                    <?= $news['content'] ?>
                </div>
            <?php elseif ($news['summary']): ?>
                <div class="news-detail-body">
                    <p><?= nl2br(clean($news['summary'])) ?></p>
                </div>
            <?php else: ?>
                <div class="news-detail-body empty-content">
                    <p>暂无正文内容</p>
                </div>
            <?php endif; ?>

            <?php if ($news['source_url']): ?>
                <div class="news-detail-source">
                    <a href="<?= clean($news['source_url']) ?>" target="_blank" rel="noopener">🔗 查看原文</a>
                </div>
            <?php endif; ?>
        </article>
        
        <aside class="news-sidebar">
            <!-- Related Tools -->
            <?php if (!empty($related_tools)): ?>
            <div class="sidebar-section">
                <h3 class="sidebar-title">🔧 相关工具</h3>
                <div class="sidebar-tools">
                    <?php foreach ($related_tools as $tool): ?>
                        <a href="tool.php?slug=<?= urlencode($tool['slug']) ?>" class="sidebar-tool-card" target="_blank">
                            <span class="sidebar-tool-name"><?= clean($tool['name']) ?></span>
                            <span class="sidebar-tool-tagline"><?= clean(mb_substr($tool['tagline'] ?? '', 0, 30)) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Popular News -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">🔥 热门资讯</h3>
                <ul class="sidebar-list">
                    <?php foreach ($popular as $item): ?>
                        <li><a href="news_detail.php?id=<?= $item['id'] ?>" target="_blank"><?= clean(mb_substr($item['title'], 0, 25)) ?><?= mb_strlen($item['title']) > 25 ? '...' : '' ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Recent News -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">📰 最新资讯</h3>
                <ul class="sidebar-list">
                    <?php foreach ($recent as $item): ?>
                        <li><a href="news_detail.php?id=<?= $item['id'] ?>" target="_blank"><?= clean(mb_substr($item['title'], 0, 25)) ?><?= mb_strlen($item['title']) > 25 ? '...' : '' ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Quick Links -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">📂 快速导航</h3>
                <div class="sidebar-links">
                    <a href="tools.php" class="sidebar-link-btn">🛠️ 工具库</a>
                    <a href="hot.php" class="sidebar-link-btn">⭐ 热门工具</a>
                    <a href="submit.php" class="sidebar-link-btn">➕ 提交工具</a>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
.news-layout { display: grid; grid-template-columns: 1fr 300px; gap: 32px; margin: 32px auto; max-width: 1200px; }
.news-detail { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 40px; }
.back-link { display: inline-block; margin-bottom: 20px; font-size: 14px; color: var(--text-light); }
.back-link:hover { color: var(--primary); }
.news-detail-title { font-size: 28px; font-weight: 700; color: var(--text); margin-bottom: 16px; line-height: 1.4; }
.news-detail-meta { display: flex; gap: 20px; font-size: 14px; color: var(--text-light); margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
.news-detail-image { margin-bottom: 28px; border-radius: var(--radius-sm); overflow: hidden; }
.news-detail-image img { width: 100%; display: block; }
.news-detail-body { font-size: 16px; line-height: 2; color: var(--text); }
.news-detail-body p { margin-bottom: 16px; }
.news-detail-body h2 { font-size: 22px; margin: 28px 0 16px; }
.news-detail-body h3 { font-size: 18px; margin: 24px 0 12px; }
.news-detail-body img { max-width: 100%; border-radius: 8px; margin: 16px 0; }
.news-detail-body a { color: var(--primary); }
.news-detail-body blockquote { border-left: 4px solid var(--primary); padding: 12px 20px; margin: 16px 0; background: #f8fafc; border-radius: 0 8px 8px 0; color: var(--text-light); }
.news-detail-body ul, .news-detail-body ol { padding-left: 24px; margin-bottom: 16px; }
.news-detail-body li { margin-bottom: 8px; }
.empty-content { text-align: center; padding: 40px; color: var(--text-light); }
.news-detail-source { margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); }
.news-detail-source a { font-size: 15px; color: var(--primary); font-weight: 600; }
.news-detail-source a:hover { color: var(--primary-dark); }

/* Sidebar Styles */
.news-sidebar { display: flex; flex-direction: column; gap: 24px; }
.sidebar-section { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 20px; }
.sidebar-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--text); }
.sidebar-tools { display: flex; flex-direction: column; gap: 10px; }
.sidebar-tool-card { display: block; padding: 12px; border-radius: var(--radius-sm); background: var(--bg); transition: 0.2s; border: 1px solid transparent; }
.sidebar-tool-card:hover { border-color: var(--primary); transform: translateX(4px); }
.sidebar-tool-name { display: block; font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.sidebar-tool-tagline { display: block; font-size: 12px; color: var(--text-light); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sidebar-list { list-style: none; }
.sidebar-list li { margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
.sidebar-list li:last-child { border-bottom: none; }
.sidebar-list a { font-size: 13px; color: var(--text); line-height: 1.5; display: block; }
.sidebar-list a:hover { color: var(--primary); }
.sidebar-links { display: flex; flex-direction: column; gap: 8px; }
.sidebar-link-btn { display: block; padding: 10px 14px; background: var(--bg); border-radius: var(--radius-sm); font-size: 13px; color: var(--text); text-align: center; transition: 0.2s; }
.sidebar-link-btn:hover { background: var(--primary); color: white; }

/* Responsive */
@media (max-width: 1024px) {
    .news-layout { grid-template-columns: 1fr; }
    .news-sidebar { order: 2; }
}
@media (max-width: 768px) {
    .news-detail { padding: 24px 16px; }
    .news-detail-title { font-size: 22px; }
    .news-detail-meta { gap: 12px; }
}
</style>

<?php require_once 'templates/footer.php'; ?>