<?php
/**
 * 动态 sitemap 生成器
 */
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

try {
    $db = db();
    $today = date('Y-m-d');

    $static = [
        ['https://www.993899.com/index.php', '1.0', 'daily'],
        ['https://www.993899.com/tools.php', '0.9', 'daily'],
        ['https://www.993899.com/news.php', '0.9', 'daily'],
        ['https://www.993899.com/submit.php', '0.6', 'monthly'],
        ['https://www.993899.com/privacy.php', '0.3', 'yearly'],
        ['https://www.993899.com/hot.php', '0.8', 'daily'],
        ['https://www.993899.com/weekly.php', '0.8', 'weekly'],
        ['https://www.993899.com/bookmarklet.html', '0.6', 'monthly'],
    ];
    foreach ($static as $p) {
        echo "<url><loc>{$p[0]}</loc><priority>{$p[1]}</priority><changefreq>{$p[2]}</changefreq><lastmod>{$today}</lastmod></url>" . PHP_EOL;
    }

    $tools = $db->query("SELECT slug, updated_at FROM tools WHERE status=1")->fetchAll();
    foreach ($tools as $t) {
        $mod = $t['updated_at'] ? date('Y-m-d', strtotime($t['updated_at'])) : $today;
        echo "<url><loc>https://www.993899.com/tool.php?slug={$t['slug']}</loc><priority>0.7</priority><changefreq>weekly</changefreq><lastmod>{$mod}</lastmod></url>" . PHP_EOL;
    }

    $cats = $db->query("SELECT slug FROM categories")->fetchAll();
    foreach ($cats as $c) {
        echo "<url><loc>https://www.993899.com/tools.php?category={$c['slug']}</loc><priority>0.7</priority><changefreq>weekly</changefreq><lastmod>{$today}</lastmod></url>" . PHP_EOL;
    }
} catch (Exception $e) {
    // ignore
}

echo '</urlset>';
