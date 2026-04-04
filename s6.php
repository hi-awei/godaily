<?php header('Content-Type: text/plain; charset=utf-8');
$f = '/tmp/tools_debug.txt';
if (file_exists($f)) { readfile($f); } else { echo "NO FILE"; }

// Also write the debug ourselves
error_reporting(0);
require_once 'includes/db.php';
require_once 'includes/functions.php';
$q = isset($_GET['q']) ? $_GET['q'] : '';
$search = clean($q);
$db = db();
$where = "WHERE status=1";
$params = [];
if ($search) {
    $where .= " AND (name LIKE ? OR tagline LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$totalSql = "SELECT COUNT(*) FROM tools $where";
echo "\n--- COUNT SQL ---\n";
echo $totalSql . "\n";
try {
    $total = $db->query($totalSql)->fetchColumn();
    echo "COUNT result: $total\n";
} catch (Exception $e) {
    echo "COUNT ERROR: " . $e->getMessage() . "\n";
}

$perPage = defined('TOOLS_PER_PAGE') ? TOOLS_PER_PAGE : 20;
$orderBy = 'ORDER BY featured DESC, vote_count DESC';
$dataSql = "SELECT * FROM tools $where $orderBy LIMIT $perPage OFFSET 0";
echo "\n--- DATA SQL ---\n";
echo $dataSql . "\n";
try {
    $stmt = $db->prepare($dataSql);
    $stmt->execute($params);
    $tools = $stmt->fetchAll();
    echo "DATA result: " . count($tools) . " rows\n";
    foreach ($tools as $t) echo "  {$t['name']}\n";
} catch (Exception $e) {
    echo "DATA ERROR: " . $e->getMessage() . "\n";
}