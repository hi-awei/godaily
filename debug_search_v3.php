<?php
// Exact same logic as tools.php but output raw HTML
error_reporting(0);
require_once 'includes/db.php';
require_once 'includes/functions.php';

$search = isset($_GET['q']) ? clean($_GET['q']) : '';
$category = isset($_GET['category']) ? clean($_GET['category']) : '';
$page = 1;
$perPage = defined('TOOLS_PER_PAGE') ? TOOLS_PER_PAGE : 20;

$db = db();
$where = "WHERE status=1";
$params = [];

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

$orderBy = 'ORDER BY featured DESC, vote_count DESC';
$total = $db->query("SELECT COUNT(*) FROM tools $where")->fetchColumn();
$offset = ($page - 1) * $perPage;
$sql = "SELECT * FROM tools $where $orderBy LIMIT $perPage OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$tools = $stmt->fetchAll();

echo "empty(tools)=" . (empty($tools) ? "TRUE" : "FALSE") . "\n";
echo "count(tools)=" . count($tools) . "\n";

if (!empty($tools)) {
    foreach ($tools as $tool) {
        echo "TOOL: id={$tool['id']} name={$tool['name']} slug={$tool['slug']} icon=" . ($tool['icon'] ?? 'NULL') . "\n";
    }
} else {
    echo "NO RESULTS\n";
}

// Also check: maybe TOOLS_PER_PAGE is 0?
echo "TOOLS_PER_PAGE=" . (defined('TOOLS_PER_PAGE') ? TOOLS_PER_PAGE : 'NOT_DEFINED') . "\n";
