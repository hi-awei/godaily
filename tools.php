<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/jsonld.php';

$pageTitle = 'AI工具库';
$pageDesc = '收录297+款精选AI工具，涵盖AI对话、AI绘画、AI编程、大模型等分类。快速找到适合你的AI工具。';
$currentPage = 'tools';
$category = isset($_GET['category']) ? clean($_GET['category']) : '';
$search = isset($_GET['q']) ? clean($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$page = max(1, intval(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = TOOLS_PER_PAGE;

try {
    $db = db();
    $categories = $db->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();

    $where = "WHERE status=1";
    $params = array();
    if ($category) {
        $where .= " AND category=?";
        $params[] = $category;
    }
    if ($search) {
        $where .= " AND (name LIKE ? OR tagline LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($sort === 'new') {
        $orderBy = 'ORDER BY created_at DESC';
    } elseif ($sort === 'popular') {
        $orderBy = 'ORDER BY vote_count DESC';
    } elseif ($sort === 'rating') {
        $orderBy = 'ORDER BY rating DESC';
    } else {
        $orderBy = 'ORDER BY featured DESC, vote_count DESC';
    }

    $stmtCount = $db->prepare("SELECT COUNT(*) FROM tools $where");
    $stmtCount->execute($params);
    $total = $stmtCount->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT * FROM tools $where $orderBy LIMIT $perPage OFFSET $offset";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $tools = $stmt->fetchAll();

} catch (Exception $e) {
    $tools = array();
    $categories = array();
    $total = 0;
}

// 生成 JSON-LD
if (!empty($tools)) {
    $jsonld = jsonld_tools_list($tools, $category);
}

require_once 'templates/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">AI工具库</h1>
        <p class="page-desc">发现并比较最优秀的AI工具，找到最适合你的那一款</p>
    </div>
</div>

<div class="container">
    <div class="filter-bar">
        <form action="tools.php" method="get" class="filter-form">
            <input type="text" name="q" value="<?php echo $search; ?>" placeholder="搜索工具..." class="filter-input">
            <?php if ($category): ?>
                <input type="hidden" name="category" value="<?php echo $category; ?>">
            <?php endif; ?>
            <button type="submit" class="filter-btn">🔍</button>
        </form>
        <div class="filter-tabs">
            <a href="tools.php" class="<?php echo !$category ? 'active' : ''; ?>">全部</a>
            <?php foreach ($categories as $cat): ?>
                <a href="tools.php?category=<?php echo $cat['slug']; ?>"
                   class="<?php echo $category === $cat['slug'] ? 'active' : ''; ?>">
                    <?php echo $cat['icon']; ?> <?php echo $cat['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="sort-bar">
            <span>排序:</span>
            <a href="?sort=default" class="<?php echo $sort==='default'?'active':''; ?>">推荐</a>
            <a href="?sort=new" class="<?php echo $sort==='new'?'active':''; ?>">最新</a>
            <a href="?sort=popular" class="<?php echo $sort==='popular'?'active':''; ?>">最热</a>
            <a href="?sort=rating" class="<?php echo $sort==='rating'?'active':''; ?>">评分</a>
        </div>
    </div>

    <?php if ($search || $category): ?>
        <div class="filter-status">
            <?php if ($search): ?>
                <span class="filter-tag">关键词: <?php echo clean($search); ?> <a href="?category=<?php echo $category; ?>">×</a></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($tools)): ?>
        <div class="empty-state">
            <p>😕 暂无符合条件的工具</p>
            <a href="tools.php" class="btn-secondary">查看全部工具</a>
        </div>
    <?php else: ?>
        <div class="tools-grid">
            <?php foreach ($tools as $tool): ?>
                <div class="tool-card">
                    <?php if ($tool['featured']): ?>
                        <div class="featured-badge">⭐</div>
                    <?php endif; ?>
                    <div class="tool-logo">
                        <?php if (!empty($tool['icon'])): ?>
                            <img src="<?php echo $tool['icon']; ?>" alt="<?php echo clean($tool['name']); ?>" class="tool-icon-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="tool-logo-placeholder" style="display:none;"><?php echo mb_substr($tool['name'], 0, 1); ?></div>
                        <?php else: ?>
                            <div class="tool-logo-placeholder"><?php echo mb_substr($tool['name'], 0, 1); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="tool-info">
                        <h3 class="tool-name">
                            <a href="tool.php?slug=<?php echo $tool['slug']; ?>" target="_blank" rel="noopener"><?php echo clean($tool['name']); ?></a>
                        </h3>
                        <p class="tool-tagline"><?php echo clean($tool['tagline']); ?></p>
                        <div class="tool-meta">
                            <span class="tool-category" style="color:<?php echo category_color($tool['category']); ?>">
                                <?php echo category_name($tool['category']); ?>
                            </span>
                            <span class="tool-pricing <?php echo $tool['pricing']; ?>">
                                <?php
                                if ($tool['pricing'] === 'free') {
                                    echo '🆓 免费';
                                } elseif ($tool['pricing'] === 'paid') {
                                    echo '💰 付费';
                                } else {
                                    echo '⚡ 免费版';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php echo pagination($total, $page, $perPage, 'tools.php'); ?>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
