<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

echo "PHP版本: " . PHP_VERSION . "\n";
echo "curl扩展: " . (extension_loaded('curl') ? '已加载' : '未加载') . "\n";
echo "allow_url_fopen: " . ini_get('allow_url_fopen') . "\n";

if (extension_loaded('curl')) {
    echo "\n测试百度API连接...\n";
    $ch = curl_init("http://data.zz.baidu.com/urls?site=https://www.993899.com&token=TDOFRVv0z1ajN9GA");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "https://www.993899.com/");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/plain']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP: $httpCode\n";
    echo "结果: $result\n";
    echo "错误: $error\n";
} else {
    echo "\n尝试用 file_get_contents...\n";
    $ctx = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "https://www.993899.com/"
    ]]);
    $result = @file_get_contents("http://data.zz.baidu.com/urls?site=https://www.993899.com&token=TDOFRVv0z1ajN9GA", false, $ctx);
    echo "结果: $result\n";
}
