<?php
try {
    $filePath = '/www/wwwroot/web01.com/assets/icons/claude.png';
    if (!file_exists($filePath)) {
        echo "FILE_NOT_EXIST\n";
    } else {
        $size = filesize($filePath);
        echo "SIZE=$size\n";
        $content = file_get_contents($filePath);
        echo "CONTENT_LEN=" . strlen($content) . "\n";
        echo "CONTENT_HEX=" . bin2hex($content) . "\n";
        echo "FIRST_50=" . substr($content, 0, 50) . "\n";
    }
    
    // 也试试通过 URL 读
    $urlContent = @file_get_contents('https://993899.com/assets/icons/claude.png');
    echo "\nURL_CONTENT_LEN=" . strlen($urlContent) . "\n";
    echo "URL_ERROR=" . error_get_last()['message'] . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
