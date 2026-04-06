<?php
error_reporting(0);
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/ad.php';
require_once 'includes/jsonld.php';

$pageTitle = '首页';
$pageDesc = 'GoDaily 每日更新，收录297+款精选AI工具，为你发现最值得使用的AI产品。涵盖AI对话、AI绘画、AI编程、大模型等分类。';
$pageKeywords = 'AI工具,AI导航,人工智能工具,ChatGPT,Claude,Midjourney,AI工具导航';
$currentPage = 'home';
$jsonld = jsonld_homepage();

try {
    $db = db();
    $featuredTools = $db->query("SELECT * FROM tools WHERE status=1 AND icon IS NOT NULL AND icon != '' ORDER BY featured DESC, vote_count DESC LIMIT 12")->fetchAll();
    $newTools = $db->query("SELECT * FROM tools WHERE status=1 AND category != 'ai-research' ORDER BY created_at DESC LIMIT 8")->fetchAll();
    $hotNews = $db->query("SELECT * FROM news WHERE status=1 ORDER BY is_hot DESC, published_at DESC LIMIT 5")->fetchAll();
    $categoryStats = $db->query("SELECT category, COUNT(*) as cnt FROM tools WHERE status=1 GROUP BY category ORDER BY cnt DESC")->fetchAll();
    $totalTools = $db->query("SELECT COUNT(*) FROM tools WHERE status=1")->fetchColumn();
} catch (Exception $e) {
    $featuredTools = $newTools = $hotNews = $categoryStats = [];
    $totalTools = 0;
}

require_once 'templates/header.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1 class="hero-title">发现最优质的 <span class="highlight">AI 工具</span></h1>
        <p class="hero-sub">GoDaily 每日更新，收录 <?= number_format($totalTools) ?>+ 款精选AI工具，为你发现最值得使用的AI产品</p>
        <form action="tools.php" method="get" class="hero-search">
            <input type="text" name="q" placeholder="搜索工具名称、功能..." class="hero-search-input">
            <button type="submit" class="hero-search-btn">搜索</button>
        </form>
        <div class="hero-tags">
            <span>热门:</span>
            <?php 
            $catNames = [
                'productivity' => '效率工具', 'ai-office' => 'AI办公', 'ai-chat' => 'AI对话',
                'ai-research' => '学术研究', 'ai-image' => 'AI绘画', 'ai-audio' => 'AI音频',
                'ai-video' => 'AI视频', 'llm' => '大模型', 'ai-design' => 'AI设计',
                'ai-code' => 'AI编程', 'ai-prompt' => '提示词', 'ai-learning' => 'AI学习',
                'image' => '图像处理', 'video' => '视频处理', 'coding' => '编程开发',
                'writing' => '写作辅助', 'audio' => '音频处理', 'other' => '其他',
                'design' => '设计工具', 'marketing' => '营销推广'
            ];
            foreach ($categoryStats as $cat): 
                $cname = $catNames[$cat['category']] ?? $cat['category'];
            ?>
                <a href="tools.php?category=<?= $cat['category'] ?>"><?= $cname ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Tools -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">⭐ 精选工具</h2>
            <a href="tools.php" class="section-more">查看全部 →</a>
        </div>
        <div class="featured-grid">
            <?php if (empty($featuredTools)): ?>
                <div class="coming-soon"><p>暂无精选工具</p></div>
            <?php else: foreach ($featuredTools as $tool): ?>
                <div class="tool-card">
                    <div class="tool-logo">
                        <?php if (!empty($tool['icon'])): ?>
                            <img src="<?= $tool['icon'] ?>" alt="<?= clean($tool['name']) ?>" class="tool-icon-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="tool-logo-placeholder" style="display:none;"><?= mb_substr($tool['name'], 0, 1) ?></div>
                        <?php else: ?>
                            <div class="tool-logo-placeholder"><?= mb_substr($tool['name'], 0, 1) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="tool-info">
                        <h3 class="tool-name"><a href="tool.php?slug=<?= $tool['slug'] ?>" target="_blank" rel="noopener"><?= clean($tool['name']) ?></a></h3>
                        <p class="tool-tagline"><?= clean($tool['tagline']) ?></p>
                        <div class="tool-meta">
                            <?php 
                            $catNames = [
                                'productivity' => '效率工具', 'ai-office' => 'AI办公', 'ai-chat' => 'AI对话',
                                'ai-research' => '学术研究', 'ai-image' => 'AI绘画', 'ai-audio' => 'AI音频',
                                'ai-video' => 'AI视频', 'llm' => '大模型', 'ai-design' => 'AI设计',
                                'ai-code' => 'AI编程', 'ai-prompt' => '提示词', 'ai-learning' => 'AI学习',
                                'image' => '图像处理', 'video' => '视频处理', 'coding' => '编程开发',
                                'writing' => '写作辅助', 'audio' => '音频处理', 'other' => '其他',
                                'design' => '设计工具', 'marketing' => '营销推广'
                            ];
                            $cname = $catNames[$tool['category']] ?? $tool['category'];
                            ?>
                            <span class="tool-category" style="color:<?= category_color($tool['category']) ?>"><?= $cname ?></span>
                            <?php if ($tool['pricing'] === 'free'): ?>
                                <span class="tool-pricing free">🆓 免费</span>
                            <?php elseif ($tool['pricing'] === 'paid'): ?>
                                <span class="tool-pricing paid">💰 付费</span>
                            <?php else: ?>
                                <span class="tool-pricing">⚡ 免费版</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<!-- Two Column -->
<section class="section">
    <div class="container">
        <div class="two-col-grid">
            <div class="col">
                <div class="section-header">
                    <h2 class="section-title">🆕 最新工具</h2>
                    <a href="tools.php?sort=new" class="section-more">更多 →</a>
                </div>
                <div class="latest-list">
                    <?php if (empty($newTools)): ?>
                        <p class="empty-state">暂无新工具</p>
                    <?php else: foreach ($newTools as $tool): ?>
                        <div class="latest-item">
                            <div class="latest-logo">
                                <?php if (!empty($tool['icon'])): ?>
                                    <img src="<?= $tool['icon'] ?>" alt="<?= clean($tool['name']) ?>" class="tool-icon-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="tool-logo-placeholder small" style="display:none;"><?= mb_substr($tool['name'], 0, 1) ?></div>
                                <?php else: ?>
                                    <div class="tool-logo-placeholder small"><?= mb_substr($tool['name'], 0, 1) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="latest-info">
                                <a href="tool.php?slug=<?= $tool['slug'] ?>" target="_blank" rel="noopener" class="latest-name"><?= clean($tool['name']) ?></a>
                                <span class="latest-tagline"><?= clean($tool['tagline']) ?></span>
                            </div>
                            <span class="latest-time"><?= time_ago($tool['created_at']) ?></span>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <div class="col">
                <div class="section-header">
                    <h2 class="section-title">📰 AI资讯</h2>
                    <a href="news.php" class="section-more">更多 →</a>
                </div>
                <div class="news-list">
                    <?php if (empty($hotNews)): ?>
                        <p class="empty-state">暂无资讯</p>
                    <?php else: foreach ($hotNews as $news): ?>
                        <div class="news-item <?= $news['is_hot'] ? 'hot' : '' ?>">
                            <?php if ($news['is_hot']): ?><span class="hot-tag">🔥 热门</span><?php endif; ?>
                            <a href="news_detail.php?id=<?= $news['id'] ?>" target="_blank" rel="noopener" class="news-title"><?= clean($news['title']) ?></a>
                            <div class="news-meta">
                                <span><?= clean($news['source']) ?></span>
                                <span><?= time_ago($news['published_at']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Submit CTA -->
<section class="section submit-cta">
    <div class="container">
        <div class="cta-box">
            <h2>📬 有你喜欢的工具没收录？</h2>
            <p>告诉我们，我们会在24小时内审核并添加</p>
            <a href="submit.php" class="btn-primary">立即提交</a>
        </div>
    </div>
</section>

<!-- Load AdSense ads -->
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

<?php require_once 'templates/footer.php'; ?>
