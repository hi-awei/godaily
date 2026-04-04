<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
$pageTitle = '隐私政策';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle) ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= SITE_URL ?>/index.php" class="logo">
                <span class="logo-icon">🚀</span>
                <span class="logo-text">GoDaily</span>
            </a>
        </div>
    </header>
    <main class="main-content">
        <div class="container" style="max-width:800px;padding:60px 20px">
            <h1 style="font-size:28px;margin-bottom:24px">隐私政策</h1>
            <div style="font-size:15px;line-height:2;background:white;padding:32px;border-radius:12px">
                <p>更新时间：2026年4月3日</p>
                <h2 style="margin-top:24px">信息收集</h2>
                <p>我们收集您主动提交的工具信息（名称、网址、描述）用于网站展示。提交时记录您的IP地址以防止滥用。</p>
                <h2 style="margin-top:24px">Cookie使用</h2>
                <p>我们使用Cookie来维护管理后台登录状态，不用于追踪用户行为。</p>
                <h2 style="margin-top:24px">第三方链接</h2>
                <p>网站包含指向第三方工具官网的链接，这些第三方有自己的隐私政策，我们不对其行为负责。</p>
                <h2 style="margin-top:24px">信息保护</h2>
                <p>我们采取合理的安全措施保护您的个人信息，未经您的同意不会向第三方出售或泄露。</p>
                <h2 style="margin-top:24px">联系我们</h2>
                <p>如对隐私政策有疑问，请联系：<a href="mailto:contact@993899.com">contact@993899.com</a></p>
            </div>
        </div>
    </main>
</body>
</html>
