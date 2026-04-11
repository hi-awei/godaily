<?php
/**
 * API接口：投票、评论、收藏
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$ip = get_client_ip();

// 生成用户唯一标识（IP + User-Agent 哈希）
function get_user_hash() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return hash('sha256', $ip . $ua);
}

try {
    $db = db();

    // ==================== 投票 ====================
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

    // ==================== 评论 ====================
    
    // 获取评论列表
    if ($action === 'comments') {
        $toolId = intval($_GET['tool_id'] ?? 0);
        if (!$toolId) {
            echo json_encode(['success' => false, 'message' => 'Invalid tool']);
            exit;
        }

        $stmt = $db->prepare("
            SELECT id, nickname, content, rating, created_at 
            FROM comments 
            WHERE tool_id=? AND status=1 AND parent_id=0 
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$toolId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 获取回复
        foreach ($comments as &$comment) {
            $stmt = $db->prepare("
                SELECT id, nickname, content, created_at 
                FROM comments 
                WHERE parent_id=? AND status=1 
                ORDER BY created_at ASC
            ");
            $stmt->execute([$comment['id']]);
            $comment['replies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 获取统计
        $stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE tool_id=? AND status=1");
        $stmt->execute([$toolId]);
        $total = $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT AVG(rating) FROM comments WHERE tool_id=? AND status=1 AND rating>0");
        $stmt->execute([$toolId]);
        $avgResult = $stmt->fetchColumn();
        $avgRating = $avgResult ? round(floatval($avgResult), 1) : 0;

        echo json_encode([
            'success' => true,
            'comments' => $comments,
            'total' => $total,
            'avg_rating' => $avgRating ?: 0
        ]);
        exit;
    }

    // 提交评论
    if ($action === 'comment_add') {
        $toolId = intval($_POST['tool_id'] ?? 0);
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $rating = intval($_POST['rating'] ?? 0);
        $parentId = intval($_POST['parent_id'] ?? 0);

        if (!$toolId || !$nickname || !$content) {
            echo json_encode(['success' => false, 'message' => '请填写必要信息']);
            exit;
        }

        if (mb_strlen($content) < 5 || mb_strlen($content) > 1000) {
            echo json_encode(['success' => false, 'message' => '评论内容需要5-1000字']);
            exit;
        }

        if ($rating < 0 || $rating > 5) {
            $rating = 0;
        }

        // 过滤敏感词（简单版）
        $badWords = ['垃圾', '骗子', '傻逼', '操你', '妈的'];
        foreach ($badWords as $word) {
            if (strpos($content, $word) !== false) {
                echo json_encode(['success' => false, 'message' => '评论包含敏感词']);
                exit;
            }
        }

        $stmt = $db->prepare("
            INSERT INTO comments (tool_id, parent_id, nickname, email, content, rating, ip, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$toolId, $parentId, $nickname, $email, $content, $rating, $ip]);

        echo json_encode(['success' => true, 'message' => '评论成功']);
        exit;
    }

    // ==================== 收藏 ====================
    
    // 获取收藏状态
    if ($action === 'favorite_check') {
        $toolId = intval($_GET['tool_id'] ?? 0);
        $userHash = get_user_hash();

        $stmt = $db->prepare("SELECT id FROM favorites WHERE tool_id=? AND user_hash=?");
        $stmt->execute([$toolId, $userHash]);
        $isFavorited = $stmt->fetch() ? true : false;

        // 获取收藏数
        $stmt = $db->prepare("SELECT COUNT(*) FROM favorites WHERE tool_id=?");
        $stmt->execute([$toolId]);
        $count = $stmt->fetchColumn();

        echo json_encode([
            'success' => true,
            'is_favorited' => $isFavorited,
            'count' => $count
        ]);
        exit;
    }

    // 添加收藏
    if ($action === 'favorite_add') {
        $toolId = intval($_POST['tool_id'] ?? 0);
        $userHash = get_user_hash();

        if (!$toolId) {
            echo json_encode(['success' => false, 'message' => 'Invalid tool']);
            exit;
        }

        // 检查是否已收藏
        $stmt = $db->prepare("SELECT id FROM favorites WHERE tool_id=? AND user_hash=?");
        $stmt->execute([$toolId, $userHash]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => '已收藏']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO favorites (tool_id, user_hash, ip) VALUES (?, ?, ?)");
        $stmt->execute([$toolId, $userHash, $ip]);

        echo json_encode(['success' => true, 'message' => '收藏成功']);
        exit;
    }

    // 取消收藏
    if ($action === 'favorite_remove') {
        $toolId = intval($_POST['tool_id'] ?? 0);
        $userHash = get_user_hash();

        $stmt = $db->prepare("DELETE FROM favorites WHERE tool_id=? AND user_hash=?");
        $stmt->execute([$toolId, $userHash]);

        echo json_encode(['success' => true, 'message' => '已取消收藏']);
        exit;
    }

    // 获取用户收藏列表
    if ($action === 'favorites') {
        $userHash = $_GET['user_hash'] ?? get_user_hash();

        $stmt = $db->prepare("
            SELECT t.id, t.name, t.slug, t.tagline, t.icon, t.category, t.pricing,
                   f.created_at as favorited_at
            FROM favorites f
            JOIN tools t ON f.tool_id = t.id
            WHERE f.user_hash = ?
            ORDER BY f.created_at DESC
            LIMIT 100
        ");
        $stmt->execute([$userHash]);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'favorites' => $favorites,
            'total' => count($favorites)
        ]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
