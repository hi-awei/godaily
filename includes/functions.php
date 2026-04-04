<?php
require_once __DIR__ . '/../config.php';

function clean($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function slug($str) {
    return preg_replace('/[^\p{L}\p{Nd}\-]+/u', '-', strtolower(trim($str)));
}

function pagination($total, $page, $perPage, $baseUrl) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';

    $html = '<div class="pagination">';
    $range = 2;
    $start = max(1, $page - $range);
    $end = min($totalPages, $page + $range);

    if ($page > 1) {
        $html .= "<a href=\"{$baseUrl}?page=" . ($page - 1) . "\" class=\"prev\">&laquo; 上一页</a>";
    }
    if ($start > 1) {
        $html .= "<a href=\"{$baseUrl}?page=1\">1</a>";
        if ($start > 2) $html .= '<span class="ellipsis">...</span>';
    }
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $page ? ' class="active"' : '';
        $html .= "<a href=\"{$baseUrl}?page={$i}\"{$active}>{$i}</a>";
    }
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<span class="ellipsis">...</span>';
        $html .= "<a href=\"{$baseUrl}?page={$totalPages}\">{$totalPages}</a>";
    }
    if ($page < $totalPages) {
        $html .= "<a href=\"{$baseUrl}?page=" . ($page + 1) . "\" class=\"next\">下一页 &raquo;</a>";
    }
    $html .= '</div>';
    return $html;
}

function time_ago($datetime) {
    $ts = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return $diff . '秒前';
    if ($diff < 3600) return floor($diff / 60) . '分钟前';
    if ($diff < 86400) return floor($diff / 3600) . '小时前';
    if ($diff < 2592000) return floor($diff / 86400) . '天前';
    if ($diff < 31536000) return floor($diff / 2592000) . '个月前';
    return date('Y-m-d', $ts);
}

function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return filter_var(trim($ip), FILTER_VALIDATE_IP) ? $ip : 'unknown';
}

function category_color($cat) {
    return [
        'llm' => '#8b5cf6', 'image' => '#f59e0b', 'video' => '#ef4444',
        'audio' => '#10b981', 'coding' => '#3b82f6', 'writing' => '#ec4899',
        'productivity' => '#06b6d4', 'design' => '#f97316', 'marketing' => '#84cc16',
        'other' => '#6b7280', 'chatbot' => '#6366f1', 'search' => '#0ea5e9',
        'detector' => '#f43f5e', 'education' => '#14b8a6', 'academic' => '#a855f7',
        'ai-chatbots' => '#6366f1', 'ai-writing' => '#ec4899', 'ai-image' => '#f59e0b',
        'ai-video' => '#ef4444', 'ai-audio' => '#10b981', 'ai-coding' => '#3b82f6',
        'ai-misc' => '#6b7280',
    ][$cat] ?? '#6b7280';
}

function category_name($cat) {
    return [
        'llm' => '大模型', 'image' => '图像', 'video' => '视频',
        'audio' => '音频', 'coding' => '编程', 'writing' => '写作',
        'productivity' => '效率', 'design' => '设计', 'marketing' => '营销',
        'other' => '其他', 'chatbot' => '对话', 'search' => '搜索',
        'detector' => '检测', 'education' => '教育', 'academic' => '学术',
        'ai-chatbots' => 'AI对话', 'ai-writing' => 'AI写作', 'ai-image' => 'AI图像',
        'ai-video' => 'AI视频', 'ai-audio' => 'AI音频', 'ai-coding' => 'AI编程',
        'ai-misc' => '其他',
    ][$cat] ?? $cat;
}

function requireAuth() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit;
    }
}
