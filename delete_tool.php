<?php
// 删除无效工具
header('Content-Type: application/json');
require_once 'includes/db.php';
$db = db();

// 删除ID=24 (米哈游·星火)
$stmt = $db->prepare("UPDATE tools SET status=0 WHERE id=24");
$stmt->execute();

echo json_encode(['success' => true, 'message' => '已下架米哈游·星火']);