<?php
/**
 * API接口：投票
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$ip = get_client_ip();

try {
    $db = db();

    if ($action === 'vote') {
        $toolId = intval($_GET['tool_id'] ?? 0);
        if (!$toolId) {
            echo json_encode(['success' => false, 'message' => 'Invalid tool']);
            exit;
        }

        // 检查是否已投票
        $stmt = $db->prepare("SELECT id FROM votes WHERE tool_id=? AND ip=?");
        $stmt->execute([$toolId, $ip]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => '你已投过票了']);
            exit;
        }

        // 写入投票
        $db->prepare("INSERT INTO votes (tool_id, ip) VALUES (?, ?)")->execute([$toolId, $ip]);
        $db->prepare("UPDATE tools SET vote_count=vote_count+1 WHERE id=?")->execute([$toolId]);

        echo json_encode(['success' => true, 'message' => '投票成功']);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
