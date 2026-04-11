<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = '关于我们';
$currentPage = 'about';
$pageDesc = '了解AI导航 - 我们是谁，我们的使命，以及我们如何帮助您发现最优质的AI工具。';
$pageKeywords = '关于AI导航,AI工具导航,AI导航团队';
require_once __DIR__ . '/templates/header.php';
?>

<div class="container" style="max-width:900px;margin:0 auto;padding:40px 20px;">
    <h1 style="font-size:28px;font-weight:700;margin-bottom:30px;color:var(--text-primary);">关于 AI导航</h1>

    <div class="about-section" style="background:var(--card-bg);border-radius:12px;padding:32px;margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:16px;color:var(--text-primary);">🚀 我们的使命</h2>
        <p style="line-height:1.8;color:var(--text-secondary);font-size:16px;">
            AI导航 致力于发现和整理全球最优质的 AI 工具，帮助每个人在人工智能时代找到最适合自己的效率工具。我们相信，AI 不应该只是技术人员的专属，而应该成为每个人工作和生活中的得力助手。
        </p>
    </div>

    <div class="about-section" style="background:var(--card-bg);border-radius:12px;padding:32px;margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:16px;color:var(--text-primary);">💡 我们做什么</h2>
        <ul style="line-height:2;color:var(--text-secondary);font-size:16px;padding-left:20px;">
            <li><strong>AI 工具收录</strong>：我们持续收录最新的 AI 工具，涵盖对话、绘画、视频、编程、办公等多个领域</li>
            <li><strong>深度评测</strong>：每款工具都有详细的功能介绍和使用场景说明，帮助您快速了解是否适合自己</li>
            <li><strong>精选分类</strong>：按功能和应用场景对工具进行系统分类，方便快速查找</li>
        </ul>
    </div>

    <div class="about-section" style="background:var(--card-bg);border-radius:12px;padding:32px;margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:16px;color:var(--text-primary);">📊 网站数据</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:20px;margin-top:16px;">
            <?php
            $toolCount = isset($db) && $db ? $db->query("SELECT COUNT(*) FROM tools WHERE status=1")->fetchColumn() : 372;
            $catCount = isset($db) && $db ? $db->query("SELECT COUNT(*) FROM categories")->fetchColumn() : 12;
            ?>
            <div style="text-align:center;padding:20px;background:var(--bg);border-radius:8px;">
                <div style="font-size:32px;font-weight:700;color:var(--primary);"><?= $toolCount ?>+</div>
                <div style="font-size:14px;color:var(--text-secondary);margin-top:4px;">精选 AI 工具</div>
            </div>
            <div style="text-align:center;padding:20px;background:var(--bg);border-radius:8px;">
                <div style="font-size:32px;font-weight:700;color:var(--primary);"><?= $catCount ?></div>
                <div style="font-size:14px;color:var(--text-secondary);margin-top:4px;">工具分类</div>
            </div>
            <div style="text-align:center;padding:20px;background:var(--bg);border-radius:8px;">
                <div style="font-size:32px;font-weight:700;color:var(--primary);">持续</div>
                <div style="font-size:14px;color:var(--text-secondary);margin-top:4px;">定期更新</div>
            </div>
        </div>
    </div>

    <div class="about-section" style="background:var(--card-bg);border-radius:12px;padding:32px;margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:16px;color:var(--text-primary);">🤝 加入我们</h2>
        <p style="line-height:1.8;color:var(--text-secondary);font-size:16px;">
            如果您有任何建议、合作意向或问题反馈，欢迎通过<a href="/contact.php" style="color:var(--primary);">联系我们</a>页面与我们取得联系。
        </p>
    </div>

    <div class="about-section" style="background:var(--card-bg);border-radius:12px;padding:32px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:16px;color:var(--text-primary);">⚖️ 法律信息</h2>
        <ul style="line-height:2;color:var(--text-secondary);font-size:16px;padding-left:20px;">
            <li>网站备案号：<a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow" style="color:var(--primary);">宁ICP备2024005816号</a></li>
            <li>本站仅提供 AI 工具的导航和信息介绍服务，不提供工具本身的下载或使用服务</li>
            <li>本站收录的所有工具版权归原开发者所有</li>
            <li>详细隐私政策请查看<a href="/privacy.php" style="color:var(--primary);">隐私政策</a>页面</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
