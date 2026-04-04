<?php header('Content-Type: text/plain; charset=utf-8');
error_reporting(0);
require_once 'includes/db.php';
require_once 'includes/functions.php';
$q = isset($_GET['q']) ? $_GET['q'] : '';
$search = clean($q);
echo "q=$q search=$search\n";
$db = db();
$where = "WHERE status=1";
$params = [];
if ($search) {
    $where .= " AND (name LIKE ? OR tagline LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$total = $db->query("SELECT COUNT(*) FROM tools $where")->fetchColumn();
echo "total=$total\n";
$stmt = $db->prepare("SELECT id,name FROM tools $where LIMIT 5");
$stmt->execute($params);
$r = $stmt->fetchAll();
echo "found=" . count($r) . "\n";
foreach ($r as $t) echo " {$t['name']}\n";
echo "DONE";