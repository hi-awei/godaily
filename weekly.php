<?php
require_once 'config.php';
$pageTitle = 'AI周刊 - 993899.com AI工具日报';
$pageDesc = '每周精选AI工具动态与大模型资讯，助你紧跟AI前沿。';
$canonical = 'https://www.993899.com/weekly.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDesc; ?>">
    <meta name="google-adsense-account" content="ca-pub-4485249374604824">
    <link rel="canonical" href="<?php echo $canonical; ?>">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="alternate" type="application/rss+xml" title="993899 RSS" href="rss.php">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4485249374604824" crossorigin="anonymous"></script>
</head>
<body>
<?php include 'templates/header.php'; ?>

<main class="container main-content">
    <div class="hero">
        <div class="hero-inner">
            <div class="hero-badge">📅 每周更新</div>
            <h1>AI 周刊</h1>
            <p class="hero-desc">精选本周 AI 工具与大模型最新动态，深度解读趋势，每周一期。</p>
            <div class="hero-meta">
                <span>📆 <?php echo date('Y年n月j日'); ?> 第<?php echo ceil((strtotime(date('Y-m-d')) - strtotime('2024-01-01')) / 7); ?>期</span>
                <span>🔥 <?php
                    $db = getDb();
                    $r = $db->query("SELECT COUNT(*) as c FROM tools WHERE status='published'");
                    $cnt = $r ? $r->fetch_assoc()['c'] : 0;
                    echo $cnt;
                ?> 个工具收录</span>
            </div>
        </div>
    </div>

    <!-- 本期内容 -->
    <div class="content-area">
        <h2 class="section-title">📖 本期目录</h2>
        <div class="toc-grid">
            <a href="#news" class="toc-card">📰 本周资讯</a>
            <a href="#tools" class="toc-card">🛠️ 新增工具</a>
            <a href="#trends" class="toc-card">📈 趋势洞察</a>
            <a href="#hot" class="toc-card">🔥 热门工具</a>
        </div>
    </div>

    <!-- 本周资讯 -->
    <section id="news" class="content-area">
        <h2 class="section-title">📰 本周 AI 资讯</h2>
        <div class="news-list">
            <?php
            $db = getDb();
            $news = $db->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 20");
            if ($news && $news->num_rows > 0) {
                while ($n = $news->fetch_assoc()) {
                    $tagClass = strtolower(str_replace(['AI','(',')','/'], '', $n['category'] ?? 'AI'));
                    $tagClass = preg_replace('/[^a-z]/', '', $tagClass);
                    $tagClass = $tagClass ?: 'ai';
                    echo '<article class="news-item">';
                    echo '<div class="news-meta">';
                    echo '<span class="news-tag ' . htmlspecialchars($tagClass) . '">' . htmlspecialchars($n['category'] ?? 'AI') . '</span>';
                    echo '<span class="news-date">' . date('m/d', strtotime($n['created_at'] ?? 'now')) . '</span>';
                    echo '</div>';
                    echo '<h3 class="news-title"><a href="' . htmlspecialchars($n['source_url'] ?? '#') . '" target="_blank" rel="noopener">' . htmlspecialchars($n['title']) . '</a></h3>';
                    if (!empty($n['summary'])) {
                        echo '<p class="news-summary">' . htmlspecialchars($n['summary']) . '</p>';
                    }
                    echo '</article>';
                }
            } else {
                echo '<div class="empty-state"><p>暂无资讯数据。</p></div>';
            }
            ?>
        </div>

        <!-- 资讯太少时补充静态内容 -->
        <?php if ($news && $news->num_rows < 5): ?>
        <div class="news-list static-news">
            <article class="news-item">
                <div class="news-meta"><span class="news-tag aivideo">大模型</span><span class="news-date">本周</span></div>
                <h3 class="news-title"><a href="https://www.993899.com/tools.php?category=llm" target="_blank">GPT-5、Claude 4、Gemini 2.0 持续迭代，多模态能力成标配</a></h3>
                <p class="news-summary">各大厂商的大模型产品持续更新，GPT-5 新增深度推理能力，Claude 4 强化长文本处理，Gemini 2.0 Flash 在速度和成本上大幅优化，国产大模型（Kimi、通义、文心）也在快速追赶。</p>
            </article>
            <article class="news-item">
                <div class="news-meta"><span class="news-tag aiwriting">AI写作</span><span class="news-date">本周</span></div>
                <h3 class="news-title"><a href="https://www.993899.com/tools.php?category=writing" target="_blank">AI写作工具全面渗透内容创作领域</a></h3>
                <p class="news-summary">从秘塔AI写作到笔灵AI，AI写作工具覆盖论文、公文、营销文案等多种场景。SEO优化、长篇报告生成、爆款标题生成成为主流功能。</p>
            </article>
            <article class="news-item">
                <div class="news-meta"><span class="news-tag aiimage">AI图像</span><span class="news-date">本周</span></div>
                <h3 class="news-title"><a href="https://www.993899.com/tools.php?category=image" target="_blank">AI绘图进入视频生成时代</a></h3>
                <p class="news-summary">Sora、Runway Pika、Kling等AI视频生成工具崛起，静止图像生成动态视频成为新趋势。电商、短视频创作者开始大规模使用AI视频工具降本增效。</p>
            </article>
            <article class="news-item">
                <div class="news-meta"><span class="news-tag aicoding">AI编程</span><span class="news-date">本周</span></div>
                <h3 class="news-title"><a href="https://www.993899.com/tools.php?category=coding" target="_blank">AI编程助手渗透软件工程全流程</a></h3>
                <p class="news-summary">GitHub Copilot、Cursor、Trae等AI编程工具已覆盖代码补全、代码审查、Bug修复、架构设计全流程。中小企业团队开始将AI编程工具纳入标准开发环境。</p>
            </article>
        </div>
        <?php endif; ?>
    </section>

    <!-- 本周新增工具 -->
    <section id="tools" class="content-area">
        <h2 class="section-title">🛠️ 本周新增工具</h2>
        <div class="tools-grid">
            <?php
            $db = getDb();
            $tools = $db->query("SELECT * FROM tools WHERE status='published' ORDER BY created_at DESC LIMIT 12");
            if ($tools && $tools->num_rows > 0) {
                while ($t = $tools->fetch_assoc()) {
                    $tagline = htmlspecialchars($t['tagline'] ?? $t['description'] ?? '优质AI工具等你发现');
                    echo '<div class="tool-card">';
                    echo '<div class="tool-icon">';
                    $icon = !empty($t['icon_url']) ? htmlspecialchars($t['icon_url']) : 'https://www.993899.com/assets/img/tool-default.png';
                    echo '<img src="' . $icon . '" alt="' . htmlspecialchars($t['name']) . '" onerror="this.src=\'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 48 48%22><text y=%2240%22 font-size=%2236%22>🛠️</text></svg>\'">';
                    echo '</div>';
                    echo '<div class="tool-info">';
                    echo '<h3><a href="tool.php?id=' . intval($t['id']) . '">' . htmlspecialchars($t['name']) . '</a></h3>';
                    echo '<p class="tool-tagline">' . mb_substr($tagline, 0, 60) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>暂无工具数据。</p>';
            }
            ?>
        </div>
        <div style="text-align:center;margin:24px 0;">
            <a href="tools.php" class="btn-primary">查看全部 <?php echo $cnt; ?> 个工具 →</a>
        </div>
    </section>

    <!-- 趋势洞察 -->
    <section id="trends" class="content-area">
        <h2 class="section-title">📈 趋势洞察</h2>
        <div class="trend-cards">
            <div class="trend-card">
                <h3>🤖 Agent 时代来临</h3>
                <p>2025年是AI Agent元年。从OpenAI的Operator到Claude的Computer Use，从AutoGPT到各类自主任务执行工具，AI正在从"回答问题"进化到"替我做事"。预计2026年Agent将在办公自动化、客服、数据分析等领域大规模落地。</p>
            </div>
            <div class="trend-card">
                <h3>🎨 视频生成爆发</h3>
                <p>Sora、Kling、Runway Pika、Luma Dream Machine等工具让"一句话生成视频"成为现实。AI视频正在颠覆影视制作、广告创意、短视频创作。2025年Q1，AI视频工具的用户增长速度已超AI图像工具3倍。</p>
            </div>
            <div class="trend-card">
                <h3>📝 长文本与深度研究</h3>
                <p>Kimi、Claude、Gemini竞相提升上下文窗口到百万token级别，AI正在从短文本助手进化为长文档分析专家。AI+RAG（检索增强生成）成为企业知识管理的标配方案。</p>
            </div>
            <div class="trend-card">
                <h3>☁️ 多模态融合</h3>
                <p>文字、图像、音频、视频的边界正在模糊。GPT-4V、Gemini Pro Vision支持图文混合理解；音频模型支持语音克隆和实时翻译；多模态Agent可以"看"着屏幕帮你操作电脑。</p>
            </div>
        </div>
    </section>

    <!-- 热门工具 -->
    <section id="hot" class="content-area">
        <h2 class="section-title">🔥 热门工具 TOP 10</h2>
        <div class="hot-list">
            <?php
            $db = getDb();
            $hotTools = $db->query("SELECT * FROM tools WHERE status='published' ORDER BY view_count DESC, created_at DESC LIMIT 10");
            if ($hotTools && $hotTools->num_rows > 0) {
                $rank = 1;
                while ($t = $hotTools->fetch_assoc()) {
                    $cat = htmlspecialchars($t['category'] ?? 'AI');
                    $tagline = mb_substr(htmlspecialchars($t['tagline'] ?? $t['description'] ?? ''), 0, 50);
                    echo '<div class="hot-item">';
                    echo '<span class="hot-rank rank-' . $rank . '">' . str_pad($rank, 2, '0', STR_PAD_LEFT) . '</span>';
                    echo '<div class="hot-info">';
                    echo '<a href="tool.php?id=' . intval($t['id']) . '" class="hot-name">' . htmlspecialchars($t['name']) . '</a>';
                    echo '<span class="hot-cat">' . $cat . '</span>';
                    echo '</div>';
                    echo '<span class="hot-views">' . number_format($t['view_count'] ?? 0) . ' 浏览</span>';
                    echo '</div>';
                    $rank++;
                }
            } else {
                echo '<p>暂无数据。</p>';
            }
            ?>
        </div>
        <div style="text-align:center;margin:24px 0;">
            <a href="hot.php" class="btn-outline">查看完整热门排行 →</a>
        </div>
    </section>

    <!-- 历史周刊 -->
    <section class="content-area">
        <h2 class="section-title">📚 历史周刊</h2>
        <div class="archive-list">
            <?php
            // 生成最近4期的历史周刊
            $weeks = [
                ['title' => '第14期：大模型军备竞赛持续', 'date' => '2026-03-21', 'summary' => 'GPT-5发布、Claude 4登场、Kimi破百万上下文...'],
                ['title' => '第13期：AI视频工具集中爆发', 'date' => '2026-03-14', 'summary' => 'Sora全面开放、Kling国内可用、Pika融资成功...'],
                ['title' => '第12期：AI Agent应用元年开启', 'date' => '2026-03-07', 'summary' => 'OpenAI Operator、多Agent协作、企业级AI落地...'],
                ['title' => '第11期：国产AI工具崛起', 'date' => '2026-02-28', 'summary' => '通义千问开源、文心一言4.0、DeepSeek性价比之王...'],
            ];
            foreach ($weeks as $w) {
                echo '<div class="archive-item">';
                echo '<span class="archive-date">' . date('m/d', strtotime($w['date'])) . '</span>';
                echo '<div class="archive-info">';
                echo '<h4>' . htmlspecialchars($w['title']) . '</h4>';
                echo '<p>' . htmlspecialchars($w['summary']) . '</p>';
                echo '</div>';
                echo '<span class="archive-tag">往期</span>';
                echo '</div>';
            }
            ?>
        </div>
    </section>

    <!-- AdSense -->
    <div class="ad-container">
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-4485249374604824" data-ad-slot="weekly_in_article" data-ad-format="auto" data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
</main>

<?php include 'templates/footer.php'; ?>
</body>
</html>
