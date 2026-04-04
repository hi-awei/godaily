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
} catch (Exception $e) {
    header('Location: news.php'); exit;
}

$pageTitle = $news['title'];
$currentPage = 'news';

require_once 'templates/header.php';
?>

<div class="container">
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
</div>

<style>
.news-detail { max-width: 800px; margin: 32px auto; background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 40px; }
.back-link { display: inline-block; margin-bottom: 20px; font-size: 14px; color: var(--text-light); }
.back-link:hover { color: var(--primary); }
.news-detail-title { font-size: 28px; font-weight: 700; color: var(--text); margin-bottom: 16px; line-height: 1.4; }
.news-detail-meta { display: flex; gap: 20px; font-size: 14px; color: var(--text-light); margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
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
</style>

<?php require_once 'templates/footer.php'; ?>