<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/jsonld.php';

$slug = isset($_GET['slug']) ? clean($_GET['slug']) : '';

if (!$slug) {
    header('Location: tools.php');
    exit;
}

try {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM tools WHERE slug=? AND status=1");
    $stmt->execute(array($slug));
    $tool = $stmt->fetch();

    if (!$tool) {
        header('Location: tools.php');
        exit;
    }

    $db->prepare("UPDATE tools SET views=views+1 WHERE id=?")->execute(array($tool['id']));

    $related = $db->query("SELECT * FROM tools WHERE category='" . $tool['category'] . "' AND id!=" . $tool['id'] . " AND status=1 ORDER BY RAND() LIMIT 4")->fetchAll();
    $votes = $db->query("SELECT COUNT(*) FROM votes WHERE tool_id=" . $tool['id'])->fetchColumn();
    
    // Get popular news for sidebar
    $popular_news = $db->query("SELECT id, title FROM news WHERE status=1 ORDER BY view_count DESC LIMIT 5")->fetchAll();
    
    // Get recent news for sidebar
    $recent_news = $db->query("SELECT id, title FROM news WHERE status=1 ORDER BY published_at DESC LIMIT 5")->fetchAll();
    
    // Get popular tools for sidebar
    $popular_tools = $db->query("SELECT slug, name, tagline FROM tools WHERE status=1 ORDER BY views DESC LIMIT 5")->fetchAll();

    $pageTitle = $tool['name'];
    $pageDesc = $tool['tagline'] ?: mb_substr(strip_tags($tool['description'] ?? ''), 0, 160);
    $pageKeywords = $tool['name'] . ',AI工具,' . $tool['category'];
    $jsonld = jsonld_tool($tool);
    $pageDesc = $tool['tagline'] . ' - ' . $tool['name'] . '详细介绍、评分、价格及使用方法。' . SITE_DESC;

} catch (Exception $e) {
    header('Location: tools.php');
    exit;
}

require_once 'templates/header.php';
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">首页</a> &gt;
        <a href="tools.php">工具库</a> &gt;
        <a href="tools.php?category=<?php echo $tool['category']; ?>"><?php echo category_name($tool['category']); ?></a> &gt;
        <span><?php echo clean($tool['name']); ?></span>
    </div>

    <div class="tool-detail-layout">
        <div class="tool-detail-main-col">
            <div class="tool-detail">
                <div class="tool-detail-header">
                    <div class="tool-detail-logo">
                        <?php if (!empty($tool['icon'])): ?>
                            <img src="<?= $tool['icon'] ?>" alt="<?= clean($tool['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="tool-logo-placeholder large" style="display:none;"><?= mb_substr($tool['name'], 0, 1) ?></div>
                        <?php elseif (!empty($tool['logo'])): ?>
                            <img src="<?= clean($tool['logo']) ?>" alt="<?= clean($tool['name']) ?>">
                        <?php else: ?>
                            <div class="tool-logo-placeholder large"><?= mb_substr($tool['name'], 0, 1) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="tool-detail-info">
                        <h1 class="tool-detail-name"><?php echo clean($tool['name']); ?></h1>
                        <p class="tool-detail-tagline"><?php echo clean($tool['tagline']); ?></p>
                        <div class="tool-detail-badges">
                            <span class="badge" style="background:<?php echo category_color($tool['category']); ?>20;color:<?php echo category_color($tool['category']); ?>">
                                <?php echo category_name($tool['category']); ?>
                            </span>
                            <?php if ($tool['pricing'] === 'free'): ?>
                                <span class="badge free">🆓 免费</span>
                            <?php elseif ($tool['pricing'] === 'paid'): ?>
                                <span class="badge paid">💰 付费</span>
                            <?php else: ?>
                                <span class="badge freemium">⚡ 免费版可用</span>
                            <?php endif; ?>
                            <?php if ($tool['pricing_detail']): ?>
                                <span class="badge detail"><?php echo clean($tool['pricing_detail']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tool-detail-actions">
                        <a href="<?php echo clean($tool['url']); ?>" target="_blank" class="btn-primary btn-large">访问官网 →</a>
                        <button class="btn-vote" onclick="vote(<?php echo $tool['id']; ?>)">👍 投票 (<?php echo $votes; ?>)</button>
                    </div>
                </div>

                <div class="tool-detail-body">
                    <div class="tool-detail-main">
                        <div class="card">
                            <h2>📝 详细介绍</h2>
                            <div class="tool-description"><?php
                                $desc = trim(strip_tags($tool['description'] ?? ''));
                                $tagline = trim($tool['tagline'] ?? '');
                                // 占位符或空描述：用tagline代替
                                $isPlaceholder = strlen($desc) < 20 || preg_match('/优秀的AI工具|具有强大的功能|良好的用户体验|业界广受好评|适合各类用户/i', $desc);
                                if ($isPlaceholder && $tagline) {
                                    echo '<p class="tool-tagline-highlight">' . clean($tagline) . '</p>';
                                    echo '<p style="color:var(--text-light);font-size:13px;margin-top:8px;">（暂无详细描述，欢迎访问官网了解更多）</p>';
                                } else {
                                    echo nl2br(clean($tool['description']));
                                }
                            ?></div>
                        </div>

                        <?php if (!empty($related)): ?>
                        <div class="card">
                            <h2>👇 同类推荐</h2>
                            <div class="related-tools">
                                <?php foreach ($related as $r): ?>
                                    <a href="tool.php?slug=<?php echo $r['slug']; ?>" class="related-item" target="_blank">
                                        <div class="related-logo">
                                            <?php if (!empty($r['icon'])): ?>
                                                <img src="<?= $r['icon'] ?>" alt="<?= clean($r['name']) ?>" class="tool-icon-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                                <div class="tool-logo-placeholder small" style="display:none;"><?= mb_substr($r['name'], 0, 1) ?></div>
                                            <?php else: ?>
                                                <div class="tool-logo-placeholder small"><?= mb_substr($r['name'], 0, 1) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-info">
                                            <span class="related-name"><?php echo clean($r['name']); ?></span>
                                            <span class="related-tagline"><?php echo clean($r['tagline']); ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <aside class="tool-detail-sidebar">
                        <div class="card stats-card">
                            <h3>📊 数据统计</h3>
                            <div class="stat-row"><span>浏览量</span><strong><?php echo number_format($tool['views']); ?></strong></div>
                            <div class="stat-row"><span>投票数</span><strong><?php echo number_format($votes); ?></strong></div>
                            <div class="stat-row"><span>用户评分</span><strong><?php echo number_format($tool['rating'], 1); ?>/5</strong></div>
                            <div class="stat-row"><span>收录时间</span><strong><?php echo date('Y-m-d', strtotime($tool['created_at'])); ?></strong></div>
                        </div>
                        <div class="card">
                            <h3>📰 最新资讯</h3>
                            <ul class="sidebar-list">
                                <?php foreach ($recent_news as $n): ?>
                                    <li><a href="news_detail.php?id=<?= $n['id'] ?>" target="_blank"><?= clean(mb_substr($n['title'], 0, 22)) ?><?= mb_strlen($n['title']) > 22 ? '...' : '' ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
</div>

<script>
function vote(toolId) {
    fetch('api.php?action=vote&tool_id=' + toolId)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) {
                alert('投票成功！');
                location.reload();
            } else {
                alert(d.message || '投票失败');
            }
        });
}
function copyLink() {
    navigator.clipboard.writeText(location.href).then(function() { alert('链接已复制！'); });
}
</script>

<style>
.tool-detail-layout { display: grid; grid-template-columns: 1fr 280px; gap: 24px; margin: 24px auto; }
.tool-detail-main-col { min-width: 0; }
.tool-detail-sidebar-new { display: flex; flex-direction: column; gap: 20px; }
.tool-detail-sidebar-new .sidebar-section { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 18px; }
.tool-detail-sidebar-new .sidebar-title { font-size: 15px; font-weight: 700; margin-bottom: 14px; color: var(--text); }
.tool-detail-sidebar-new .sidebar-list { list-style: none; }
.tool-detail-sidebar-new .sidebar-list li { margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
.tool-detail-sidebar-new .sidebar-list li:last-child { border-bottom: none; }
.tool-detail-sidebar-new .sidebar-list a { font-size: 13px; color: var(--text); line-height: 1.4; display: block; }
.tool-detail-sidebar-new .sidebar-list a:hover { color: var(--primary); }
.tool-detail-sidebar-new .sidebar-tools { display: flex; flex-direction: column; gap: 8px; }
.tool-detail-sidebar-new .sidebar-tool-card { display: block; padding: 10px; border-radius: var(--radius-sm); background: var(--bg); transition: 0.2s; }
.tool-detail-sidebar-new .sidebar-tool-card:hover { transform: translateX(4px); border-left: 3px solid var(--primary); }
.tool-detail-sidebar-new .sidebar-tool-name { display: block; font-size: 13px; font-weight: 600; color: var(--text); }
.tool-detail-sidebar-new .sidebar-tool-tagline { display: block; font-size: 11px; color: var(--text-light); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tool-detail-sidebar-new .sidebar-links { display: flex; flex-direction: column; gap: 8px; }
.tool-detail-sidebar-new .sidebar-link-btn { display: block; padding: 10px 12px; background: var(--bg); border-radius: var(--radius-sm); font-size: 13px; color: var(--text); text-align: center; transition: 0.2s; }
.tool-detail-sidebar-new .sidebar-link-btn:hover { background: var(--primary); color: white; }
@media (max-width: 1024px) { .tool-detail-layout { grid-template-columns: 1fr; } .tool-detail-sidebar-new { order: 2; } }
</style>

<?php require_once 'templates/footer.php'; ?>