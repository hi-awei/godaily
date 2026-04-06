<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/ad.php';
require_once __DIR__ . '/../includes/jsonld.php';
$siteUrl = rtrim(SITE_URL, '/');
$pageTitleHtml = isset($pageTitle) ? clean($pageTitle) . ' - ' . SITE_NAME : SITE_NAME;
$cur = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $pageTitleHtml ?></title>
    <meta name="description" content="<?= $pageDesc ?? SITE_DESC ?>">
    <meta name="keywords" content="<?= $pageKeywords ?? SITE_KEYWORDS ?>">
    <link rel="canonical" href="<?= $siteUrl ?>">
    <meta property="og:title" content="<?= isset($pageTitle) ? clean($pageTitle) : SITE_NAME ?>">
    <meta property="og:description" content="<?= $pageDesc ?? SITE_DESC ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $siteUrl ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitleHtml ?>">
    <meta name="twitter:description" content="<?= $pageDesc ?? SITE_DESC ?>">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚀</text></svg>">
    <meta name="google-adsense-account" content="ca-pub-4485249374604824">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?= $siteUrl ?>/assets/css/style.css">
    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4485249374604824" crossorigin="anonymous"></script>
    <?= $jsonld ?? '' ?>
    <!-- 百度统计 -->
    <script>
    var _hmt = _hmt || [];
    (function() {
     var hm = document.createElement("script");
     hm.src = "https://hm.baidu.com/hm.js?2ff024957c3e9214e571475f0f0a9458";
     var s = document.getElementsByTagName("script")[0]; 
     s.parentNode.insertBefore(hm, s);
    })();
    </script>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= $siteUrl ?>/index.php" class="logo">
                <span class="logo-icon">🚀</span>
                <span class="logo-text">GoDaily</span>
            </a>
            <nav class="main-nav">
                <a href="<?= $siteUrl ?>/index.php" class="<?= $cur === 'home' ? 'active' : '' ?>">首页</a>
                <a href="<?= $siteUrl ?>/tools.php" class="<?= $cur === 'tools' ? 'active' : '' ?>">工具库</a>
                <a href="<?= $siteUrl ?>/hot.php" class="<?= $cur === 'hot' ? 'active' : '' ?>">🔥 热门</a>
                <a href="<?= $siteUrl ?>/weekly.php" class="<?= $cur === 'weekly' ? 'active' : '' ?>">📅 周刊</a>
                <a href="<?= $siteUrl ?>/news.php" class="<?= $cur === 'news' ? 'active' : '' ?>">资讯</a>
                <a href="<?= $siteUrl ?>/submit.php" class="<?= $cur === 'submit' ? 'active' : '' ?>">提交工具</a>
            </nav>
            <div class="header-actions">
                <form action="<?= $siteUrl ?>/tools.php" method="get" class="search-form">
                    <input type="text" name="q" placeholder="搜索AI工具..." class="search-input">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        </div>
    </header>
    
    <!-- Mobile Navigation -->
    <nav class="mobile-nav" id="mobileNav">
        <a href="<?= $siteUrl ?>/index.php" class="<?= $cur === 'home' ? 'active' : '' ?>">首页</a>
        <a href="<?= $siteUrl ?>/tools.php" class="<?= $cur === 'tools' ? 'active' : '' ?>">工具库</a>
        <a href="<?= $siteUrl ?>/hot.php" class="<?= $cur === 'hot' ? 'active' : '' ?>">🔥 热门</a>
        <a href="<?= $siteUrl ?>/weekly.php" class="<?= $cur === 'weekly' ? 'active' : '' ?>">📅 周刊</a>
        <a href="<?= $siteUrl ?>/news.php" class="<?= $cur === 'news' ? 'active' : '' ?>">资讯</a>
        <a href="<?= $siteUrl ?>/submit.php" class="<?= $cur === 'submit' ? 'active' : '' ?>">提交工具</a>
        <div class="mobile-search">
            <form action="<?= $siteUrl ?>/tools.php" method="get" class="mobile-search-form">
                <input type="text" name="q" placeholder="搜索AI工具..." class="mobile-search-input">
                <button type="submit" class="btn-primary">搜索</button>
            </form>
        </div>
    </nav>
    
    <main class="main-content">
