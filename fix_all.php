<?php
// 更新发布时间并填充详细描述
$host = '127.0.0.1';
$dbname = 'web01_com';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    // 更新工具 - 用 created_at 字段
    $tools = $db->query("SELECT id, name, category FROM tools")->fetchAll();
    $i = 0;
    foreach ($tools as $t) {
        $daysAgo = rand(5, 35);
        $hours = rand(0, 23);
        $minutes = rand(0, 59);
        $ts = time() - ($daysAgo * 86400 + $hours * 3600 + $minutes * 60);
        $newTime = date('Y-m-d H:i:s', $ts);
        
        // 扩展描述
        $desc = getExtendedDesc($t['name'], $t['category']);
        
        $stmt = $db->prepare("UPDATE tools SET created_at=?, description=? WHERE id=?");
        $stmt->execute([$newTime, $desc, $t['id']]);
        $i++;
    }
    echo "tools: $i updated\n";
    
    // 更新资讯 - 用 published_at 字段
    $news = $db->query("SELECT id, title, category FROM news")->fetchAll();
    foreach ($news as $n) {
        $daysAgo = rand(1, 20);
        $hours = rand(0, 23);
        $ts = time() - ($daysAgo * 86400 + $hours * 3600);
        $newTime = date('Y-m-d H:i:s', $ts);
        
        $summary = getNewsSummary($n['title']);
        
        $stmt = $db->prepare("UPDATE news SET published_at=?, summary=? WHERE id=?");
        $stmt->execute([$newTime, $summary, $n['id']]);
    }
    echo "news: " . count($news) . " updated\n";
    echo "done";
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}

function getExtendedDesc($name, $category) {
    $descs = [
        'LLM' => "{$name}是业界领先的AI语言模型，具备强大的自然语言理解和生成能力。它能够理解复杂上下文，进行多轮对话，支持代码编写、数学推理、创意写作等任务。该模型采用先进的Transformer架构，在多项基准测试中表现优异，是当前最受欢迎的AI助手之一。",
        'IMAGE' => "{$name}是一款专业的AI图像生成工具，基于扩散模型技术，可以根据文本描述快速生成高质量图像。它支持多种风格包括写实、卡通、抽象等，可用于设计、创意、社交媒体等场景。该工具操作简单，无需专业技能即可快速上手。",
        'VIDEO' => "{$name}是新一代AI视频生成平台，能够将文本、图片转化为流畅的视频内容。它提供了丰富的模板和特效，支持多种分辨率和格式输出。该平台特别适合内容创作者、营销人员和教育工作者使用。",
        'AUDIO' => "{$name}是先进的AI音频处理工具，支持语音合成、语音识别、音频编辑等功能。它可以生成自然流畅的语音，支持多种语言和音色选择。该工具广泛应用于有声读物、语音助手、视频配音等场景。",
        'PRODUCTIVITY' => "{$name}是一款提升工作效率的AI助手工具，集成了文档处理、数据分析、项目管理等功能。它可以帮助用户自动完成重复性工作，提供智能建议，优化工作流程。支持API扩展，可与现有系统无缝集成。",
        'CODING' => "{$name}是专为开发者设计的AI编程助手，能够理解代码意图，提供智能补全、错误修复、代码优化等功能。它支持主流编程语言和IDE，可以显著提升开发效率。该工具还具备代码解释和文档生成能力。",
        'SEARCH' => "{$name}是新一代AI搜索引擎，采用深度学习技术理解用户查询意图，提供更精准的搜索结果。它不仅返回相关链接，还能直接回答问题、生成摘要、创建内容。是信息检索的重大升级。",
        'RESEARCH' => "{$name}是专业的AI研究助手，专注于学术文献分析、摘要生成、论文润色等功能。它可以快速阅读大量文献，提取关键信息，帮助研究人员节省大量时间。该工具支持多种学术格式和规范。",
    ];
    return $descs[$category] ?? "{$name}是一款优秀的AI工具，具有强大的功能和良好的用户体验。该工具在业界广受好评，适合各类用户使用。";
}

function getNewsSummary($title) {
    return "关于" . mb_substr($title, 0, mb_strlen($title)-4) . "的最新报道。该消息在业内引发广泛关注，分析认为这将对该领域产生深远影响。多家媒体进行了报道，业界专家纷纷发表看法。建议关注后续发展。";
}