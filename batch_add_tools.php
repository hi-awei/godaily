<?php
error_reporting(E_ALL);
require_once __DIR__ . '/includes/db.php';

$db = db();

$tools = [
    // LLM / 大语言模型
    ['ChatGPT', 'chatgpt', 'OpenAI 开发的最强 AI 对话助手，支持 GPT-4o 模型', 'OpenAI', 'https://chatgpt.com', 'llm', 'freemium', 'ChatGPT 是由 OpenAI 开发的大型语言模型，支持多轮对话、代码生成、写作创作等场景，是目前最流行的 AI 工具之一。'],
    ['Claude', 'claude', 'Anthropic 推出的安全可靠的 AI 助手', 'Anthropic', 'https://claude.ai', 'llm', 'freemium', 'Claude 是 Anthropic 开发的大语言模型，以安全性高、长文本理解能力强著称，适合复杂推理和写作任务。'],
    ['Gemini', 'gemini', 'Google 推出的多模态 AI 助手', 'Google', 'https://gemini.google.com', 'llm', 'free', 'Google Gemini 是 Google 推出的多模态 AI 模型，支持文本、图像、视频等多种模态，已集成到 Google 产品中。'],
    ['DeepSeek', 'deepseek', '国产顶级开源大模型，支持超长上下文', 'DeepSeek', 'https://deepseek.com', 'llm', 'free', 'DeepSeek 是国内领先的 AI 大模型公司，开源的 DeepSeek-V3 和 DeepSeek-R1 性能卓越，价格低廉。'],
    ['Kimi (月之暗面)', 'kimi', '支持 20 万字超长上下文', 'Moonshot AI', 'https://kimi.moonshot.cn', 'llm', 'free', 'Kimi 是月之暗面公司开发的 AI 对话助手，以超长上下文著称，支持 20 万字无损上下文窗口。'],
    ['通义千问', 'tongyi', '阿里云推出的大语言模型', '阿里巴巴', 'https://tongyi.aliyun.com', 'llm', 'free', '通义千问是阿里巴巴推出的大语言模型，已开源 Qwen 系列，能力对标 GPT-4，支持中文场景。'],
    ['文心一言', 'wenxin', '百度出品国产大模型', '百度', 'https://yiyan.baidu.com', 'llm', 'free', '文心一言是百度基于文心大模型推出的生成式 AI 产品，深度融合百度搜索和知识图谱。'],
    ['智谱清言', 'zhipu', '清华大学 KEG 实验室出品的国产大模型', '智谱 AI', 'https://www.zhipuai.cn', 'llm', 'free', '智谱清言由清华大学 KEG 实验室和智谱 AI 共同研发，中文能力出色，已开源 ChatGLM 系列。'],
    ['讯飞星火', 'xfyun', '科大讯飞推出的认知大模型', '科大讯飞', 'https://xinghuo.xfyun.cn', 'llm', 'free', '讯飞星火是科大讯飞推出的认知大模型，支持语音交互，在语音识别和合成领域有独特优势。'],
    // AI 图像
    ['Midjourney', 'midjourney', '最强 AI 图像生成工具', 'Midjourney', 'https://www.midjourney.com', 'image', 'paid', 'Midjourney 是目前最强大的 AI 图像生成工具之一，通过 Discord 交互，生成质量惊艳艺术感十足的作品。'],
    ['Stable Diffusion', 'stable-diffusion', '开源免费最强 AI 绘图模型', 'Stability AI', 'https://stability.ai', 'image', 'free', 'Stable Diffusion 是开源的 AI 图像生成模型，可本地部署，完全免费，是 AI 绘画领域的里程碑。'],
    ['DALL-E 3', 'dall-e', 'OpenAI 官方 AI 图像生成', 'OpenAI', 'https://openai.com/dall-e-3', 'image', 'paid', 'DALL-E 3 是 OpenAI 推出的最新图像生成模型，与 ChatGPT 深度集成，理解复杂描述能力极强。'],
    ['Flux', 'flux', '黑马开源图像生成模型', 'Black Forest Labs', 'https://flux.ai', 'image', 'freemium', 'Flux 由前 Stable Diffusion 团队成员创立，生成质量极高，开源版本性能强大，引起业界轰动。'],
    ['Adobe Firefly', 'adobe-firefly', 'Adobe 创意云 AI 图像生成', 'Adobe', 'https://firefly.adobe.com', 'image', 'freemium', 'Adobe Firefly 是 Adobe 推出的创意 AI 工具，深度集成 Photoshop 和 Illustrator，支持中文提示词。'],
    ['Canva AI', 'canva', '在线设计平台集成 AI 功能', 'Canva', 'https://www.canva.com', 'image', 'freemium', 'Canva 的 AI 功能包括文字转图像、AI 生成设计、魔法橡皮擦等，让设计小白也能快速产出专业作品。'],
    ['Viggle', 'viggle', 'AI 人物动画生成工具', 'Viggle AI', 'https://viggle.ai', 'video', 'free', 'Viggle 可以让静态人物照片动起来，支持多种动作模板，生成的视频效果自然流畅。'],
    // AI 视频
    ['Sora', 'sora', 'OpenAI 推出的 AI 视频生成模型', 'OpenAI', 'https://openai.com/sora', 'video', 'paid', 'Sora 是 OpenAI 推出的文本转视频模型，可以根据文字描述生成长达 60 秒的高清视频。'],
    ['Pika', 'pika', 'AI 视频生成平台', 'Pika Labs', 'https://pika.art', 'video', 'freemium', 'Pika 是专注于 AI 视频生成的平台，操作简单，支持图生视频和文生视频，生成速度快。'],
    ['Runway', 'runway', '专业 AI 视频创作平台', 'Runway', 'https://runwayml.com', 'video', 'paid', 'Runway 是专业的 AI 视频创作平台，提供 Gen-2、Gen-3 等强大视频生成模型，被众多电影制作团队使用。'],
    ['HeyGen', 'heygen', 'AI 数字人视频生成', 'HeyGen', 'https://heygen.com', 'video', 'paid', 'HeyGen 可以快速生成 AI 数字人播报视频，支持多语言、多角色，是企业营销视频利器。'],
    ['即梦 AI', 'jimeng', '字节跳动 AI 图像和视频生成', '字节跳动', 'https://jimeng.jianying.com', 'video', 'freemium', '即梦是字节跳动推出的 AI 创作平台，支持中文场景的图像和视频生成，完全免费使用。'],
    // AI 音频
    ['ElevenLabs', 'elevenlabs', '最强 AI 语音克隆与合成', 'ElevenLabs', 'https://elevenlabs.io', 'audio', 'freemium', 'ElevenLabs 提供极高质量的 AI 语音合成和声音克隆，支持 28 种语言，是配音和语音内容创作者的首选。'],
    ['米哈游·星火', 'mihoyo-ai', '国产 AI 语音合成工具', '米哈游', 'https://ai.entrt.cn', 'audio', 'free', '星火是米哈游出品的 AI 语音合成工具，支持多种音色，中文语音自然流畅，完全免费。'],
    // 编程辅助
    ['GitHub Copilot', 'github-copilot', 'GitHub 官方 AI 编程助手', 'GitHub/Microsoft', 'https://github.com/features/copilot', 'coding', 'paid', 'GitHub Copilot 是由 GitHub 和 OpenAI 合作开发的 AI 编程助手，支持代码补全、注释生成代码、Bug 修复等。'],
    ['Cursor', 'cursor', 'AI 原生代码编辑器', 'Cursor', 'https://cursor.com', 'coding', 'freemium', 'Cursor 是专为 AI 协作设计的代码编辑器，基于 VS Code，集成了 GPT-4、Claude 等模型，编程效率大幅提升。'],
    ['Codeium', 'codeium', '免费 AI 代码补全插件', 'Codeium', 'https://codeium.com', 'coding', 'free', 'Codeium 是免费的 AI 代码补全工具，支持 70+ 编程语言，提供 VS Code、JetBrains 等主流 IDE 插件。'],
    ['通义灵码', 'lingma', '阿里云 AI 编程助手', '阿里巴巴', 'https://tongyi.aliyun.com/lingma', 'coding', 'free', '通义灵码是阿里巴巴推出的 AI 编程助手，支持代码补全、注释生成代码、技术问答，免费使用。'],
    // AI 搜索
    ['Perplexity', 'perplexity', 'AI 驱动的智能搜索引擎', 'Perplexity AI', 'https://perplexity.ai', 'search', 'freemium', 'Perplexity 是 AI 搜索引擎，能直接给出带来源引用的答案，比传统搜索更高效，支持中文。'],
    ['秘塔 AI 搜索', 'metaso', '国产 AI 搜索引擎', '秘塔科技', 'https://metaso.cn', 'search', 'free', '秘塔 AI 搜索是国产 AI 搜索引擎，无广告，直达结果，支持深度研究和学术搜索。'],
    ['Kimi 探索版', 'kimi-explore', 'Kimi 深度搜索能力', 'Moonshot AI', 'https://kimi.moonshot.cn', 'search', 'free', 'Kimi 探索版提供深度 AI 搜索能力，可以主动分析信息缺口并迭代搜索策略。'],
    // 效率工具
    ['Notion AI', 'notion-ai', '笔记软件集成 AI 助手', 'Notion', 'https://notion.so', 'productivity', 'paid', 'Notion AI 是 Notion 笔记软件内置的 AI 功能，可以帮你写文案、总结笔记、生成待办事项。'],
    [' Otter.ai', 'otter-ai', 'AI 会议记录和转录', 'Otter', 'https://otter.ai', 'productivity', 'freemium', 'Otter.ai 提供实时语音转文字、自动生成会议摘要、多语言翻译功能，大幅减少会议纪要时间。'],
    ['Gamma', 'gamma', 'AI 生成 PPT 演示文稿', 'Gamma', 'https://gamma.app', 'productivity', 'freemium', 'Gamma 是 AI 驱动的 PPT 生成工具，输入主题即可生成精美幻灯片，支持在线协作编辑。'],
    ['Beautiful.ai', 'beautiful-ai', 'AI 智能 PPT 设计平台', 'Beautiful.ai', 'https://www.beautiful.ai', 'productivity', 'paid', 'Beautiful.ai 提供智能 PPT 设计模板，AI 自动调整布局和配色，让演示文稿自动变得专业美观。'],
    ['腾讯文档 AI', 'tencent-docs-ai', '腾讯文档内置 AI 助手', '腾讯', 'https://docs.qq.com', 'productivity', 'free', '腾讯文档 AI 是腾讯文档内置的 AI 助手，支持智能写作、润色、总结、数据分析，深度集成微信场景。'],
    // 写作助手
    ['Grammarly', 'grammarly', 'AI 英语写作助手', 'Grammarly', 'https://grammarly.com', 'writing', 'freemium', 'Grammarly 是全球最流行的 AI 写作辅助工具，实时检查英文语法、拼写、语气并给出修改建议。'],
    ['秘塔写作猫', 'xiezuocat', '国产 AI 中文写作助手', '秘塔科技', 'https://xiezuocat.com', 'writing', 'freemium', '秘塔写作猫是专为中文写作设计的 AI 工具，支持错别字纠正、句式优化、润色降重等功能。'],
    ['讯飞写作', 'xfyun-write', '科大讯飞 AI 写作工具', '科大讯飞', 'https://writing.xfyun.cn', 'writing', 'free', '讯飞写作是科大讯飞推出的 AI 写作助手，支持多种文稿类型，中文写作能力强。'],
    // 其他
    ['Copilot Pro', 'copilot-pro', 'Microsoft 365 AI 助手', 'Microsoft', 'https://copilot.microsoft.com', 'productivity', 'paid', 'Copilot Pro 是 Microsoft 365 套件的 AI 助手，在 Word、Excel、PPT、Outlook 等办公软件中提供 AI 辅助。'],
    ['Coze', 'coze', '字节跳动 AI Bot 开发平台', '字节跳动', 'https://coze.cn', 'productivity', 'free', 'Coze（扣子）是字节跳动推出的 AI Bot 创建平台，无需编程即可快速搭建 AI 对话机器人。'],
    ['Dify', 'dify', '开源 LLM 应用开发平台', 'Dify.AI', 'https://dify.ai', 'productivity', 'free', 'Dify 是开源的 LLM 应用开发平台，支持可视化编排 AI 工作流，快速构建 AI 应用原型。'],
    ['Coze 中文版', 'coze-cn', '扣子 AI Bot 平台', '字节跳动', 'https://coze.cn', 'productivity', 'free', '扣子是字节跳动推出的 AI Bot 创建平台，支持多种大模型一键切换，无需编程即可搭建 AI 应用。'],
    ['硅基流动', 'siliconflow', '国产 AI 模型 API 聚合平台', 'SiliconFlow', 'https://siliconflow.cn', 'other', 'freemium', '硅基流动聚合了国内外顶尖大模型 API，价格低廉，稳定性好，是开发者调用 AI 能力的高性价比选择。'],
    ['Groq', 'groq', '全球最快 AI 推理平台', 'Groq', 'https://groq.com', 'other', 'freemium', 'Groq 提供 LPU 驱动的极速 AI 推理服务，Token 生成速度远超其他平台，适合对延迟敏感的应用。'],
];

$count = 0;
$errors = [];

foreach ($tools as $t) {
    list($name, $slug, $tagline, $source, $url, $category, $pricing, $desc) = $t;
    
    // 检查是否已存在
    $existing = $db->prepare("SELECT id FROM tools WHERE slug=?");
    $existing->execute([$slug]);
    if ($existing->fetch()) {
        continue;
    }
    
    $featured = in_array($slug, ['chatgpt','claude','gemini','midjourney','stable-diffusion','cursor','perplexity','kimi','dify']) ? 1 : 0;
    $is_hot = in_array($slug, ['chatgpt','claude','deepseek','midjourney','sora','cursor','perplexity','kimi']) ? 1 : 0;
    
    $sql = "INSERT INTO tools (name,slug,tagline,description,url,category,pricing,featured,is_hot,status,vote_count,created_at) VALUES (?,?,?,?,?,?,?,?,?,1,0,NOW())";
    try {
        $db->prepare($sql)->execute([$name,$slug,$tagline,$desc,$url,$category,$pricing,$featured,$is_hot]);
        $count++;
    } catch (Exception $e) {
        $errors[] = "$name: " . $e->getMessage();
    }
}

echo "成功添加 $count 个工具\n";
if ($errors) {
    echo "失败:\n";
    foreach ($errors as $e) echo " - $e\n";
}
?>
