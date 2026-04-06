<?php
/**
 * 热门AI工具排行 - 动态版
 */
$pageTitle = '热门AI工具排行';
$pageDesc = '精选最受欢迎的AI工具，包括ChatGPT、Claude、Midjourney、Cursor等，按访问量和热度排序。';
$pageKeywords = '热门AI工具,AI排行,AI工具推荐,ChatGPT,Claude,Midjourney';
$currentPage = 'hot';

require_once 'includes/db.php';
require_once 'includes/functions.php';

$db = db();
$hotTools = $db->query("SELECT * FROM tools WHERE status=1 ORDER BY views DESC LIMIT 24")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - GoDaily</title>
    <meta name="description" content="<?= $pageDesc ?>">
    <meta name="keywords" content="<?= $pageKeywords ?>">
    <link rel="canonical" href="https://www.993899.com/hot.php">
    <link rel="alternate" type="application/rss+xml" title="GoDaily RSS" href="/rss.php">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚀</text></svg>">
    <meta name="google-adsense-account" content="ca-pub-4485249374604824">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4485249374604824" crossorigin="anonymous"></script>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/index.php" class="logo">
                <span class="logo-icon">🚀</span>
                <span class="logo-text">GoDaily</span>
            </a>
            <nav class="main-nav">
                <a href="/index.php">首页</a>
                <a href="/tools.php">工具库</a>
                <a href="/hot.php" class="active">🔥 热门</a>
                <a href="/weekly.php">📅 周刊</a>
                <a href="/news.php">资讯</a>
                <a href="/submit.php">提交工具</a>
            </nav>
            <div class="header-actions">
                <form action="/tools.php" method="get" class="search-form">
                    <input type="text" name="q" placeholder="搜索AI工具..." class="search-input">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        </div>
    </header>

    <nav class="mobile-nav" id="mobileNav">
        <a href="/index.php">首页</a>
        <a href="/tools.php">工具库</a>
        <a href="/hot.php" class="active">🔥 热门</a>
        <a href="/weekly.php">📅 周刊</a>
        <a href="/news.php">资讯</a>
        <a href="/submit.php">提交工具</a>
        <div class="mobile-search">
            <form action="/tools.php" method="get" class="mobile-search-form">
                <input type="text" name="q" placeholder="搜索AI工具..." class="mobile-search-input">
                <button type="submit" class="btn-primary">搜索</button>
            </form>
        </div>
    </nav>

    <main class="main-content">
    <div class="container" style="max-width:1200px;">
        <div style="text-align:center;margin-bottom:32px;">
            <h1 style="font-size:28px;color:#1e293b;margin-bottom:8px;">🔥 热门 AI 工具排行</h1>
            <p style="color:#64748b;font-size:15px;">精选最受欢迎的 AI 工具，按访问量排序，持续更新</p>
        </div>

        <?php if (!empty($hotTools)): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
            <?php foreach ($hotTools as $i => $tool):
                $rank = $i + 1;
                $catName = category_name($tool['category']);
                $catColor = category_color($tool['category']);
                $pricingLabels = ['free'=>'🆓 免费','freemium'=>'⚡ 免费版可用','paid'=>'💰 付费','contact'=>'📞 联系定价'];
                $pricingLabel = $pricingLabels[$tool['pricing']] ?? '🆓 免费';
                $pricingClass = $tool['pricing'] === 'paid' ? 'paid' : ($tool['pricing'] === 'free' ? 'free' : 'freemium');
                $desc = trim(strip_tags($tool['description'] ?? ''));
                $tagline = trim($tool['tagline'] ?? '');
                $isPlaceholder = strlen($desc) < 20 || preg_match('/优秀的AI工具|具有强大的功能|良好的用户体验|业界广受好评|适合各类用户/i', $desc);
                if ($isPlaceholder) $desc = '';
            ?>
            <a href="/tool.php?slug=<?= urlencode($tool['slug']) ?>" class="card" style="background:white;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:0.2s;cursor:pointer;text-decoration:none;color:inherit;display:block;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div style="width:48px;height:48px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:24px;overflow:hidden;flex-shrink:0;">
                        <?php if (!empty($tool['icon']) && strpos($tool['icon'], 'http') === 0): ?>
                            <img src="<?= clean($tool['icon']) ?>" alt="<?= clean($tool['name']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <?= mb_substr($tool['name'], 0, 1) ?>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:16px;font-weight:600;color:#1e293b;"><?= clean($tool['name']) ?></div>
                        <div style="font-size:12px;color:#64748b;">#<?= $rank ?> · <span style="color:<?= $catColor ?>"><?= $catName ?></span></div>
                    </div>
                </div>
                <div style="font-size:13px;color:#475569;margin-bottom:8px;"><?= clean($tagline) ?></div>
                <?php if ($desc): ?>
                <div style="font-size:12px;color:#94a3b8;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:8px;"><?= clean($desc) ?></div>
                <?php endif; ?>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9;">
                    <span style="font-size:12px;padding:3px 8px;border-radius:6px;font-weight:500;<?php
                        if ($pricingClass === 'free') echo 'background:#dcfce7;color:#16a34a;';
                        elseif ($pricingClass === 'paid') echo 'background:#fee2e2;color:#dc2626;';
                        else echo 'background:#fef9c3;color:#ca8a04;';
                    ?>"><?= $pricingLabel ?></span>
                    <span style="font-size:12px;color:#94a3b8;">👀 <?= number_format($tool['views']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:60px 20px;color:#64748b;">
            <h2 style="font-size:20px;margin-bottom:12px;">暂无工具数据</h2>
            <p>稍后再来看看吧</p>
        </div>
        <?php endif; ?>
    </div>
    </main>

<?php require_once 'templates/footer.php'; ?>
</body>
</html>
