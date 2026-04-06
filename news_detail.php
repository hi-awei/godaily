<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=web01_com;charset=utf8mb4", "web01_com", "3FT7Ppatfp19XbAh");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    header('Location: news.php'); exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: news.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM news WHERE id=? AND status=1");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$news) { header('Location: news.php'); exit; }

$pdo->prepare("UPDATE news SET view_count=view_count+1 WHERE id=?")->execute([$id]);

$stmt_pop = $pdo->prepare("SELECT id, title FROM news WHERE status=1 ORDER BY id DESC LIMIT 5");
$stmt_pop->execute();
$popular = $stmt_pop->fetchAll(PDO::FETCH_ASSOC);

$stmt_rec = $pdo->prepare("SELECT id, title FROM news WHERE status=1 ORDER BY published_at DESC LIMIT 5");
$stmt_rec->execute();
$recent = $stmt_rec->fetchAll(PDO::FETCH_ASSOC);

$stmt_t = $pdo->prepare("SELECT slug, name, tagline FROM tools WHERE status='published' ORDER BY id DESC LIMIT 5");
$stmt_t->execute();
$related_tools = $stmt_t->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8');
$currentPage = 'news';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - GoDaily</title>
    <meta name="description" content="<?= htmlspecialchars(mb_substr($news['summary'] ?? $news['title'], 0, 120), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="google-adsense-account" content="ca-pub-4485249374604824">
    <link rel="canonical" href="https://www.993899.com/news.php?id=<?= intval($news['id']) ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="/rss.php">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4485249374604824" crossorigin="anonymous"></script>
</head>
<body>
<?php include 'templates/header.php'; ?>

<div class="container">
    <div class="news-layout">
        <article class="news-detail">
            <div class="news-detail-header">
                <a href="news.php" class="back-link">&larr; 返回列表</a>
                <h1 class="news-detail-title"><?= $pageTitle ?></h1>
                <div class="news-detail-meta">
                    <span>发布日期：<?= date('Y-m-d', strtotime($news['published_at'] ?? 'now')) ?></span>
                    <?php if ($news['source']): ?><span>来源：<?= htmlspecialchars($news['source'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <span>阅读：<?= number_format($news['view_count'] ?? 0) ?></span>
                </div>
            </div>
            <?php if ($news['image']): ?>
            <div class="news-detail-image">
                <img src="<?= htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= $pageTitle ?>">
            </div>
            <?php endif; ?>
            <?php if ($news['content']): ?>
                <div class="news-detail-body"><?= $news['content'] ?></div>
            <?php elseif ($news['summary']): ?>
                <div class="news-detail-body"><p><?= nl2br(htmlspecialchars($news['summary'], ENT_QUOTES, 'UTF-8')) ?></p></div>
            <?php else: ?>
                <div class="news-detail-body"><p>内容待补充</p></div>
            <?php endif; ?>
            <?php if ($news['source_url']): ?>
                <div class="news-detail-source">
                    <a href="<?= htmlspecialchars($news['source_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">阅读原文</a>
                </div>
            <?php endif; ?>

            <!-- Share buttons -->
            <div class="share-section">
                <span style="font-size:13px;color:#666;">分享：</span>
                <a href="https://service.weibo.com/share/share.php?url=<?= urlencode('https://www.993899.com/news.php?id='.$news['id']) ?>&title=<?= urlencode($news['title']) ?>" target="_blank" rel="noopener" style="display:inline-block;padding:6px 14px;background:#e6162d;color:#fff;border-radius:4px;font-size:13px;text-decoration:none;">微博</a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://www.993899.com/news.php?id='.$news['id']) ?>&text=<?= urlencode($news['title']) ?>" target="_blank" rel="noopener" style="display:inline-block;padding:6px 14px;background:#1da1f2;color:#fff;border-radius:4px;font-size:13px;text-decoration:none;">Twitter/X</a>
                <a href="javascript:void(0)" onclick="navigator.clipboard.writeText(location.href);this.textContent='Copied!';setTimeout(function(){this.textContent='Copy Link'},2000);" style="display:inline-block;padding:6px 14px;background:#555;color:#fff;border-radius:4px;font-size:13px;text-decoration:none;cursor:pointer;">复制链接</a>
            </div>
        </article>

        <aside class="news-sidebar">
            <?php if (!empty($related_tools)): ?>
            <div class="sidebar-section">
                <h3 class="sidebar-title">热门工具</h3>
                <?php foreach ($related_tools as $t): ?>
                    <a href="tool.php?slug=<?= urlencode($t['slug']) ?>" class="sidebar-tool-card" target="_blank">
                        <span class="tool-name"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="tool-tagline"><?= htmlspecialchars(mb_substr($t['tagline'] ?? '', 0, 30), ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($popular)): ?>
            <div class="sidebar-section">
                <h3 class="sidebar-title">热门资讯</h3>
                <ul class="sidebar-list">
                <?php foreach ($popular as $p): ?>
                    <li><a href="news.php?id=<?= intval($p['id']) ?>" target="_blank"><?= htmlspecialchars(mb_substr($p['title'], 0, 40), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($p['title']) > 40 ? '...' : '' ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($recent)): ?>
            <div class="sidebar-section">
                <h3 class="sidebar-title">最新资讯</h3>
                <ul class="sidebar-list">
                <?php foreach ($recent as $r): ?>
                    <li><a href="news.php?id=<?= intval($r['id']) ?>" target="_blank"><?= htmlspecialchars(mb_substr($r['title'], 0, 40), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($r['title']) > 40 ? '...' : '' ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="sidebar-section">
                <h3 class="sidebar-title">快速导航</h3>
                <div class="sidebar-links">
                    <a href="tools.php" class="sidebar-link-btn" target="_blank">全部工具</a>
                    <a href="hot.php" class="sidebar-link-btn" target="_blank">热门工具</a>
                    <a href="submit.php" class="sidebar-link-btn" target="_blank">提交工具</a>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
.news-layout{display:grid;grid-template-columns:1fr 300px;gap:32px;max-width:1200px;margin:0 auto;padding:32px 16px}
.news-detail{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.08);padding:32px}
.news-detail-header{margin-bottom:24px;border-bottom:1px solid #eee;padding-bottom:20px}
.back-link{font-size:14px;color:#4f46e5;text-decoration:none;display:inline-block;margin-bottom:12px}
.back-link:hover{text-decoration:underline}
.news-detail-title{font-size:26px;font-weight:700;line-height:1.4;margin-bottom:12px}
.news-detail-meta{display:flex;gap:16px;font-size:13px;color:#888;flex-wrap:wrap}
.news-detail-image{margin:20px 0;border-radius:8px;overflow:hidden}
.news-detail-image img{width:100%;height:auto;display:block}
.news-detail-body{font-size:15px;line-height:1.8;color:#333}
.news-detail-body p{margin-bottom:16px}
.news-detail-body h3{font-size:18px;margin:24px 0 12px}
.news-detail-source{margin-top:20px;padding-top:16px;border-top:1px solid #eee}
.news-detail-source a{color:#4f46e5;font-size:14px}
.news-sidebar{display:flex;flex-direction:column;gap:20px}
.sidebar-section{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.08);padding:20px}
.sidebar-title{font-size:15px;font-weight:700;margin-bottom:14px}
.sidebar-tools{display:flex;flex-direction:column;gap:8px}
.sidebar-tool-card{display:block;padding:10px 12px;background:#f9fafb;border-radius:6px;border:1px solid transparent;transition:0.2s;text-decoration:none}
.sidebar-tool-card:hover{border-color:#4f46e5;transform:translateX(4px)}
.tool-name{display:block;font-size:13px;font-weight:600;margin-bottom:2px;color:#333}
.tool-tagline{display:block;font-size:11px;color:#888;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sidebar-list{list-style:none;padding:0;margin:0}
.sidebar-list li{margin-bottom:8px;border-bottom:1px solid #eee;padding-bottom:8px}
.sidebar-list li:last-child{border-bottom:none}
.sidebar-list a{font-size:13px;color:#333;line-height:1.5;display:block}
.sidebar-list a:hover{color:#4f46e5}
.sidebar-links{display:flex;flex-direction:column;gap:8px}
.sidebar-link-btn{display:block;padding:10px 14px;background:#f9fafb;border-radius:6px;font-size:13px;text-align:center;transition:0.2s;color:#333;text-decoration:none}
.sidebar-link-btn:hover{background:#4f46e5;color:#fff}
.share-section{margin:24px 0;padding:16px 0;border-top:1px solid #eee;border-bottom:1px solid #eee;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
@media(max-width:1024px){.news-layout{grid-template-columns:1fr}}
@media(max-width:768px){.news-detail{padding:20px 16px}.news-detail-title{font-size:20px}}
</style>

<?php include 'templates/footer.php'; ?>
