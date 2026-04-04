<?php header('Content-Type: text/plain; charset=utf-8');
// Read the debug file that tools.php writes now
$f = '/tmp/tools_debug.txt';
echo file_exists($f) ? file_get_contents($f) : 'NO DEBUG FILE';
echo "\n--- Direct test ---\n";
require_once 'includes/db.php';
require_once 'includes/functions.php';
$search = '元宝';
$where = "WHERE status=1";
$params = [];
if ($search) {
    $where .= " AND (name LIKE ? OR tagline LIKE ? OR description LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}
$perPage = defined('TOOLS_PER_PAGE') ? TOOLS_PER_PAGE : 20;
$stmtCount = $db->prepare("SELECT COUNT(*) FROM tools $where");
$stmtCount->execute($params);
$total = $stmtCount->fetchColumn();
echo "total=$total\n";
$stmt2 = $db->prepare("SELECT id,name FROM tools $where ORDER BY featured DESC, vote_count DESC LIMIT $perPage OFFSET 0");
$stmt2->execute($params);
$r = $stmt2->fetchAll();
echo "found=" . count($r) . "\n";
foreach ($r as $t) echo " {$t['name']}\n";