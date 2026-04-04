<?php
// Debug tools.php search logic directly
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
require_once 'includes/functions.php';

$search = isset($_GET['q']) ? clean($_GET['q']) : '';
echo "search=[$search]\n";

$db = db();
$where = "WHERE status=1";
$params = array();

if ($search) {
    $where .= " AND (name LIKE ? OR tagline LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql = "SELECT COUNT(*) as cnt FROM tools $where";
echo "SQL: $sql\n";
echo "Params: " . json_encode($params) . "\n";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$total = $stmt->fetch()['cnt'];
echo "Total: $total\n";

$sql2 = "SELECT id, name FROM tools $where LIMIT 5";
$stmt2 = $db->prepare($sql2);
$stmt2->execute($params);
$tools = $stmt2->fetchAll();
echo "Tools found: " . count($tools) . "\n";
foreach ($tools as $t) {
    echo "  - {$t['name']}\n";
}