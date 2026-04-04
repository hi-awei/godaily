<?php
// 批量添加资讯
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$db_host = 'localhost';
$db_name = 'web01_com';
$db_user = 'web01_com';
$db_pass = '3FT7Ppatfp19XbAh';

function generateSlug($title) {
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
    $slug = trim($slug, '-');
    $slug = substr($slug, 0, 50);
    return $slug;
}

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $news = [
        ['title' => 'Midjourney V7 即将发布，官方透露将加入视频生成功能', 'source' => 'AI图像', 'content' => 'Midjourney官方宣布V7版本正在开发中，新版本将加入用户期待已久的视频生成功能。目前V6版本已经支持高质量图像生成，此次更新将是Midjourney成立以来最大的一次升级。'],
        ['title' => 'Stability AI 获得新融资，估值突破30亿美元', 'source' => 'AI日报', 'content' => 'Stability AI宣布完成新一轮融资，估值达到30亿美元。本轮融资将用于加速开源模型的研发和商业化应用。Stability AI旗下拥有Stable Diffusion、Stable Video等热门产品。'],
        ['title' => '腾讯混元大模型全面升级，API能力对标GPT-4', 'source' => 'AI前线', 'content' => '腾讯云宣布混元大模型完成重大升级，新版本在文本理解、代码生成、数学推理等多项能力上对标GPT-4。企业用户可通过腾讯云API直接调用。'],
        ['title' => 'AI视频工具 Pika 获5000万美元B轮融资', 'source' => 'AI科技资讯', 'content' => 'AI视频生成平台Pika完成5000万美元B轮融资，由知名风投a16z领投。Pika自去年上线以来已服务超过100万用户，本次融资将用于产品研发和团队扩张。'],
        ['title' => '百度文心一言用户突破1亿，位居国内AI助手首位', 'source' => 'AI工具集', 'content' => '百度官方宣布文心一言用户数突破1亿，成为国内用户规模最大的AI对话助手。百度同时发布企业版API，面向B端提供定制化服务。'],
        ['title' => 'OpenAI 推出 ChatGPT 桌面应用，支持 macOS 和 Windows', 'source' => 'AI编程', 'content' => 'OpenAI发布ChatGPT桌面客户端，支持macOS和Windows系统。新版本支持语音对话、文件分析和代码调试等高级功能，用户可离线使用基础功能。'],
        ['title' => 'Meta 发布 Llama 4，性能大幅超越 Llama 3', 'source' => 'AI日报', 'content' => 'Meta正式发布Llama 4开源大模型，新版本在多项基准测试中超越Llama 3。Llama 4提供多个参数版本，开发者可免费商用。'],
        ['title' => 'AI 音乐生成工具 Suno 推出 v4 版本，音质接近真人创作', 'source' => 'AI工具集', 'content' => 'AI音乐生成平台Suno发布v4版本，新版本生成的音乐在音质和情感表达上大幅提升。Suno v4支持更长的音乐片段和更丰富的音乐风格。'],
        ['title' => '英伟达发布新一代 AI 芯片 H200，推理性能提升2倍', 'source' => 'AI科技资讯', 'content' => '英伟达发布下一代AI芯片H200，专门针对大语言模型推理优化。新芯片相比H100在推理性能上提升2倍，能耗降低30%。'],
        ['title' => '字节跳动推出 AI 编程工具 MarsCode，面向企业开发者', 'source' => 'AI编程', 'content' => '字节跳动正式发布AI编程助手MarsCode，提供代码补全、漏洞检测和自动化重构等功能。MarsCode支持主流编程语言和企业私有化部署。']
    ];

    $now = time();
    $added = 0;
    
    foreach ($news as $n) {
        $randomDays = rand(1, 30);
        $randomHours = rand(0, 23);
        $randomMinutes = rand(0, 59);
        $publishedAt = date('Y-m-d H:i:s', $now - ($randomDays * 86400) - ($randomHours * 3600) - ($randomMinutes * 60));
        
        $slug = generateSlug($n['title']);
        $viewCount = rand(50, 500);
        
        $stmt = $db->prepare("INSERT INTO news (title, slug, source, content, view_count, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$n['title'], $slug, $n['source'], $n['content'], $viewCount, $publishedAt, $publishedAt]);
        $added++;
    }

    echo json_encode(['success' => true, 'added' => $added, 'message' => '成功添加' . $added . '篇资讯']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}