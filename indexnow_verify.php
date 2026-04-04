<?php
/**
 * IndexNow 验证文件
 * 将此文件重命名为你的 key，如 a1b2c3d4e5f6g7h8.txt
 * 内容就是 key 本身
 */

// 在这里填写你的 IndexNow Key
$key = '';

if (empty($key)) {
    http_response_code(404);
    die('Key not configured');
}

header('Content-Type: text/plain');
echo $key;
