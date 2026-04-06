<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'AI资讯';
$currentPage = 'news';
$page = max(1, intval(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = NEWS_PER_PAGE;

try {
    $db = db();
    $total = $db->query("SELECT COUNT(*) FROM news WHERE status=1")->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $news = $db->query("SELECT * FROM news WHERE status=1 ORDER BY is_hot DESC, published_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();
} catch (Exception $e) {
    $news = array();
    $total = 0;
}

require_once 'templates/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">📰 AI资讯</h1>
        <p class="page-desc">每日更新AI行业动态、产品发布和技术进展</p>
    </div>
</div>

<div class="container">
    <?php if (empty($news)): ?>
        <div class="empty-state"><p>暂无资讯，稍后再来看看</p></div>
    <?php else: ?>
        <div class="news-grid">
            <?php foreach ($news as $item): ?>
                <article class="news-card <?php echo $item['is_hot'] ? 'hot' : ''; ?>">
                    <?php if ($item['is_hot']): ?>
                        <div class="news-hot-badge">🔥 热门</div>
                    <?php endif; ?>
                    <?php if ($item['image']): ?>
                        <div class="news-image">
                            <img src="<?php echo clean($item['image']); ?>" alt="">
                        </div>
                    <?php endif; ?>
                    <div class="news-body">
                        <h2 class="news-title">
                            <a href="news_detail.php?id=<?php echo $item['id']; ?>" target="_blank" rel="noopener"><?php echo clean($item['title']); ?></a>
                        </h2>
                        <?php if ($item['summary']): ?>
                            <p class="news-summary"><?php echo clean($item['summary']); ?></p>
                        <?php endif; ?>
                        <div class="news-footer">
                            <div class="news-meta">
                                <span>📰 <?php echo clean($item['source']); ?></span>
                                <span>🕐 <?php echo time_ago($item['published_at']); ?></span>
                            </div>
                            <?php if ($item['tags']): ?>
                                <div class="news-tags">
                                    <?php foreach (explode(',', $item['tags']) as $tag): ?>
                                        <span class="news-tag"><?php echo trim($tag); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php echo pagination($total, $page, $perPage, 'news.php'); ?>
    <?php endif; ?>
</div>

<style>
.news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; padding-bottom: 40px; }
.news-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; transition: 0.3s; }
.news-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
.news-card.hot { border-color: #fbbf24; }
.news-hot-badge { background: #fbbf24; color: white; font-size: 12px; font-weight: 700; padding: 4px 12px; display: inline-block; }
.news-image { height: 180px; overflow: hidden; }
.news-image img { width: 100%; height: 100%; object-fit: cover; }
.news-body { padding: 20px; }
.news-title { font-size: 16px; font-weight: 700; margin-bottom: 10px; line-height: 1.5; }
.news-title a { color: var(--text); }
.news-title a:hover { color: var(--primary); }
.news-summary { font-size: 14px; color: var(--text-light); margin-bottom: 16px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.news-footer { border-top: 1px solid var(--border); padding-top: 12px; }
.news-meta { display: flex; gap: 16px; font-size: 13px; color: var(--text-light); margin-bottom: 8px; }
.news-tags { display: flex; gap: 6px; flex-wrap: wrap; }
.news-tag { padding: 2px 8px; background: #f1f5f9; border-radius: 10px; font-size: 11px; color: var(--text-light); }
</style>

<?php require_once 'templates/footer.php'; ?>
