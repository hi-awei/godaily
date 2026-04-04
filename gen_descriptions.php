<?php
/**
 * 工具描述生成脚本
 * 遍历全部工具，根据 tag/分类/tagline 生成高质量 description
 * 运行: https://993899.com/gen_descriptions.php?key=godaily2026
 */
header('Content-Type: text/plain; charset=utf-8');

if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') {
    die('Unauthorized');
}

require_once __DIR__ . '/includes/db.php';
$db = db();

$tools = $db->query("SELECT id, name, tagline, category, pricing, slug FROM tools ORDER BY id")->fetchAll();
$total = count($tools);
$updated = 0;
$skipped = 0;

foreach ($tools as $t) {
    $id = (int)$t['id'];
    $name = trim($t['name']);
    $tagline = trim($t['tagline'] ?? '');
    $category = trim($t['category'] ?? '');
    $pricing = trim($t['pricing'] ?? '');
    $slug = trim($t['slug'] ?? '');

    $existingDesc = $db->query("SELECT description FROM tools WHERE id=$id")->fetch()['description'] ?? '';
    $cleanDesc = trim(strip_tags($existingDesc));
    $isPlaceholder = strlen($cleanDesc) < 60 || preg_match('/优秀的AI工具|具有强大的功能|良好的用户体验|业界广受好评|适合各类用户/i', $cleanDesc);

    if (!$isPlaceholder && strlen($cleanDesc) > 60) {
        $skipped++;
        continue;
    }

    $desc = generateDescription($name, $tagline, $category, $pricing, $slug);
    $stmt = $db->prepare("UPDATE tools SET description=? WHERE id=$id");
    $stmt->execute([$desc]);
    $updated++;
}

$remaining = $db->query("SELECT COUNT(*) as cnt FROM tools WHERE LENGTH(REPLACE(REPLACE(REPLACE(description,'<p>',''),'</p>',''),'<br>','')) < 60")->fetch()['cnt'];
echo "Done. Total: $total | Updated: $updated | Skipped (already good): $skipped | Still short: $remaining\n";

function generateDescription($name, $tagline, $category, $pricing, $slug) {
    $priceText = '';
    if ($pricing) {
        $map = ['free'=>'免费使用','freemium'=>'有免费额度','paid'=>'付费订阅','contact'=>'需联系获取报价'];
        $priceText = $map[$pricing] ?? '';
    }

    $known = getKnownTools();
    $lowerSlug = strtolower($slug);
    $lowerName = strtolower($name);
    foreach ($known as $k => $v) {
        if (strpos($lowerSlug, $k) !== false || strpos($lowerName, $k) !== false) {
            return $v;
        }
    }

    $catDescs = [
        'llm'         => "{$name}是一款领先的AI大语言模型，支持高质量对话、文本生成、代码编写等核心能力。{$tagline}。{$priceText}，适合需要AI辅助工作的各类用户。",
        'image'       => "{$name}是一款专业的AI图像生成与处理工具，支持文生图、图生图等高级功能。{$tagline}。{$priceText}，设计师和创作者的理想助手。",
        'video'       => "{$name}专注于AI视频生成与剪辑，支持数字人、字幕生成、视频合成等功能。{$tagline}。{$priceText}，帮助创作者高效产出视频内容。",
        'audio'       => "{$name}是AI音频处理工具，支持语音合成、语音识别、音乐生成等功能。{$tagline}。{$priceText}，播客作者和内容创作者的好帮手。",
        'writing'     => "{$name}是一款AI写作助手，支持文章创作、文案撰写、翻译润色等多种功能。{$tagline}。{$priceText}，显著提升写作效率与质量。",
        'coding'      => "{$name}是AI编程辅助工具，支持代码补全、Bug修复、代码审查等能力。{$tagline}。{$priceText}，开发者提升效率的利器。",
        'productivity'=> "{$name}是一款AI效率工具，帮助用户完成文档整理、数据分析、任务管理等操作。{$tagline}。{$priceText}。",
        'marketing'   => "{$name}是AI营销工具，支持内容营销、社交媒体运营、数据分析等功能。{$tagline}。{$priceText}，营销人员的得力助手。",
        'design'      => "{$name}是AI设计工具，支持海报制作、UI设计、logo生成等功能。{$tagline}。{$priceText}，设计师的创意加速器。",
        'search'      => "{$name}是AI搜索引擎，能够理解复杂查询并返回精准结果。{$tagline}。{$priceText}，信息检索的新一代解决方案。",
        'education'   => "{$name}是AI教育工具，支持智能答疑、学习推荐、作业辅导等功能。{$tagline}。{$priceText}，学生和教师的高效学习助手。",
        'chatbot'     => "{$name}是AI对话助手，能够进行自然语言交流并提供智能回复。{$tagline}。{$priceText}。",
        'detector'    => "{$name}是AI检测工具，支持文本、图像或音视频内容的智能分析与检测。{$tagline}。{$priceText}，内容安全与质量把控的好帮手。",
        'api'         => "{$name}提供AI能力API接口，支持开发者快速集成大模型能力。{$tagline}。{$priceText}。",
    ];

    $fallback = $tagline
        ? "{$name}是{$tagline}。{$priceText}。"
        : "{$name}是一款功能强大的AI工具，适合各类用户使用。{$priceText}。";

    return $catDescs[$category] ?? $fallback;
}

function getKnownTools() {
    return [
        'chatgpt'         => 'ChatGPT是OpenAI开发的AI对话助手，支持高质量问答、文本创作、代码编写、多语言翻译等。内置GPT-4大模型，能够理解复杂上下文并生成连贯回复。免费版提供基础功能，Plus订阅每月$20解锁GPT-4及更高配额。',
        'gpt-4'           => 'GPT-4是OpenAI推出的最新一代大型语言模型，在推理、编程、创意写作、学术研究等方面能力显著提升。支持图像输入（GPT-4V），上下文窗口达128K tokens。API按token计费，适合需要高级AI能力的开发者和企业。',
        'claude'          => 'Claude是Anthropic开发的AI助手，以安全性著称。擅长长文本分析、代码审查、创意写作、学术研究。支持100K tokens超长上下文，Claude 3系列包含Haiku、Sonnet和Opus三个版本，按能力分层定价。',
        'gemini'          => 'Gemini是Google AI开发的原生多模态大模型，支持文本、图像、音频、视频统一理解。Ultra版本对标GPT-4，Pro版本性价比出色。深度集成Google搜索和数据生态，适合已使用Google服务的用户和开发者。',
        'deepseek'        => 'DeepSeek是中国团队开发的顶级开源大模型，支持超长上下文窗口和超长输出。DeepSeek-V2采用MoE架构，API价格极具竞争力。开源可商用，适合企业和开发者部署使用。',
        'kimi'            => 'Kimi是月之暗面（Moonshot AI）推出的AI对话助手，以20万字超长上下文著称。能一次性阅读并理解整本书籍、长篇报告、代码仓库。适合需要处理长文档的研究者和商务人士。',
        'wenxin'          => '文心一言是百度推出的国产大语言模型，基于文心大模型4.0技术。深度集成百度搜索和中国本土化知识，适合中文场景下的对话、创作、信息查询。支持ERNIE Bot平台调用。',
        'yi'              => '零一万物（01.AI）推出的Yi系列大模型，由李开复团队打造。Yi-34B开源可商用，在中文和英文任务上表现优异。MoE架构的Yi-Large版本API开放使用，性能对标GPT-4。',
        'qwen'            => '通义千问是阿里云推出的大语言模型，基于自研Qwen架构。支持长文本理解和生成，中文能力突出。开源版本Qwen2性能优秀，API接入阿里云DashScope平台。适合国内开发者使用。',
        'tongyi'          => '通义千问是阿里云自研的大语言模型，通过DashScope平台提供服务。开源版本Qwen2性能优异，支持百亿参数规模。',
        'spark'           => '讯飞星火是科大讯飞推出的认知大模型，基于讯飞多年语音和语言技术积累。深度整合语音识别（ASR）和语音合成（TTS）能力，在教育、医疗、会议等垂直场景表现突出。',
        'zhipu'           => '智谱清言是清华大学KEG实验室出品的国产大模型，基于ChatGLM架构。支持中英双语对话、长文本分析、代码生成。GLM-4系列API开放，性能对标GPT-4，国内调用稳定。',
        'minimax'         => 'MiniMax是国内头部AI公司开发的大模型，主打高性价比API服务。支持文本生成、对话、Embedding等功能，适合企业级AI应用集成。',
        'doubao'          => '豆包是字节跳动推出的AI对话助手，基于自研云雀大模型。深度集成抖音、飞书等字节系产品生态，适合国内用户日常使用。',
        'baichuan'        => '百川智能由王小川创立，Baichuan系列开源大模型在中文任务上表现优异。Baichuan2可商用，性能对标GPT-3.5，API服务稳定。',
        'tiangong'        => '天工AI是昆仑万维推出的国产大模型，基于自研天工大模型。支持对话、搜索、写作等功能，中文能力突出。',
        '360'             => '360智脑是360公司推出的AI大模型，集成360搜索安全数据和知识图谱。专注中文互联网场景的信息处理。',
        'kuai'            => '夸克AI是阿里智能信息业务推出的AI助手，基于夸克浏览器和搜索数据。擅长信息检索、知识问答和文档处理。',
        'huou'            => '腾讯混元是腾讯AI Lab推出的国产大模型，支持高质量中文对话和内容生成。',
        'coze'            => '扣子（Coze）是字节跳动推出的AI应用开发和托管平台，支持工作流编排、插件集成、海量Bot模板。无需编程即可创建AI聊天机器人并发布到抖音、微信等平台。',
        'cope'            => '扣子是字节跳动一站式AI Bot开发平台，支持零代码构建聊天机器人，内置丰富插件和知识库，工作流自动化。',
        'poe'             => 'Poe是Quora推出的AI聊天聚合平台，在一个界面内同时使用ChatGPT、GPT-4、Claude、Llama等所有主流AI模型。',
        'perplexity'      => 'Perplexity是AI搜索领域的标杆产品，能实时检索网络并给出带来源标注的答案。支持学术论文、新闻事件、代码问题等多种查询类型。',
        'you'              => 'You.com是由前Google员工创立的AI搜索引擎，深度集成实时网络搜索和代码执行能力。提供个性化搜索体验。',
        'phind'           => 'Phind是专为开发者设计的AI搜索引擎，擅长编程问题、API使用、框架选择等技术支持类查询。',
        'metaso'          => '秘塔AI搜索是国内AI搜索引擎，主打无广告、直达结果的搜索体验。支持学术、商业、日常等多种搜索场景。',
        'devv'            => 'Devv AI是专注于程序员的新一代AI搜索引擎，支持GitHub仓库、文档、StackOverflow等开发资源的精准检索。',
        'devin'           => 'Devin是Cognition AI推出的全球首位AI软件工程师，能够端到端完成编程任务：写代码、改Bug、部署服务、解答技术问题。在真实软件开发任务中通过多项工程基准测试。',
        'cursor'          => 'Cursor是AI代码编辑器，基于GPT-4和Claude模型构建。支持代码自动补全、多文件编辑、问答解释、代码重构。比Copilot更侧重智能生成和交互式编程。',
        'github-copilot'  => 'GitHub Copilot是GitHub与OpenAI合作的AI编程助手，深度集成VS Code等主流IDE。实时补全代码、生成函数、解释代码、撰写测试。个人版$10/月，企业版$19/人/月。',
        'copilot'         => 'Microsoft Copilot是微软AI助手，基于GPT-4构建，集成于Windows、Office 365、Bing等微软产品。',
        'codeium'         => 'Codeium是免费AI代码补全工具，支持70+编程语言。无需注册即可使用基础功能，付费版解锁更多高级功能。',
        'tabnine'         => 'TabNine是AI代码补全工具，支持本地部署（保护代码隐私）。兼容主流IDE，企业版提供私有模型训练能力。',
        'v0'              => 'v0是Vercel推出的AI前端代码生成器，通过对话式交互生成React组件、Next.js页面代码。直接复制使用或部署到Vercel。',
        'windsurf'        => 'Windsurf是Codeium推出的AI代码编辑器，定位介于Copilot和Agent之间。支持多文件智能编辑和上下文感知。',
        'claude-code'     => 'Claude Code是Anthropic官方命令行工具，通过终端直接调用Claude能力完成代码编写、调试、Git操作等任务。',
        'bolt'            => 'Bolt.new是StackBlitz推出的AI全栈开发工具，在浏览器中完成从项目创建到部署的全流程。',
        'replit'          => 'Replit是AI驱动的在线IDE，支持浏览器编程、AI自动补全、项目托管。Ghostwriter AI助手贯穿开发全流程。',
        'midjourney'      => 'Midjourney是全球最流行的AI图像生成工具之一，通过Discord平台以文字描述生成精美图像。v6版本在真实感和风格控制上有显著提升。付费订阅$10-$120/月。',
        'stable-diffusion'=> 'Stable Diffusion是Stability AI推出的开源AI图像生成模型，支持本地部署和自定义训练。LoRA、ControlNet等生态丰富，精确控制构图和姿势。',
        'dall-e'          => 'DALL-E是OpenAI开发的AI图像生成模型，最新版DALL-E 3深度集成ChatGPT，支持精确的文字理解和创意图像生成。',
        'flux'            => 'FLUX是Black Forest Labs推出的开源AI图像生成模型，真实感人像和文字渲染能力出色。社区热度迅速攀升。',
        'firefly'         => 'Adobe Firefly是Adobe Creative Cloud内置的AI图像生成工具，深度集成Photoshop、Illustrator。支持中文提示词，以商业安全使用为卖点。',
        'dreamstudio'     => 'DreamStudio是Stability AI官方图像生成平台，基于Stable Diffusion模型，提供简洁的Web界面。',
        'runway'          => 'Runway是AI视频生成和编辑平台，Gen系列模型支持文生视频、图生视频、视频风格迁移等。专业影视团队和创作者广泛使用。',
        'pika'            => 'Pika是AI视频生成工具，专注消费级视频内容创作。支持文生视频和视频风格化，操作简单易上手。',
        'kling'           => '快手可灵（Kling）是快手推出的AI视频生成大模型，支持长达3分钟的高质量视频生成，在动作连贯性和物理模拟上有突破。',
        'haiper'          => 'Haiper AI是视频生成AI初创公司推出的产品，支持文生视频和视频重绘，擅长动态艺术风格。',
        'genmo'           => 'Genmo是AI视频和3D生成平台，Mo项目可生成3D模型和动态视频。',
        'vidu'            => 'Vidu是生数科技推出的AI视频生成模型，对标Sora，支持生成长达16秒的连贯视频。',
        'luma'            => 'Luma AI的Dream Machine是AI视频生成平台，支持高质量文生视频和角色动画。',
        'sora'            => 'Sora是OpenAI开发的AI视频生成模型，能根据文字描述生成长达60秒的连贯视频，在复杂场景和角色一致性上表现突出。',
        'sunado'          => '即创是字节跳动推出的AI内容创作平台，支持短视频脚本生成、数字人视频、AI配音等功能。',
        'zhipu-video'     => '清影是智谱AI推出的视频生成模型，基于CogVideoX架构，支持中文视频生成。',
        'tencent-zhiying' => '腾讯智影是腾讯推出的在线智能视频剪辑平台，支持数字人播报、视频剪辑、字幕生成等功能。',
        '讯飞听见'        => '讯飞听见是科大讯飞旗下AI音视频处理平台，核心功能包括录音转文字、语音转写、同声传译会议等，准确率行业领先。',
        'elevenlabs'      => 'ElevenLabs是AI语音合成平台，支持120+种语言和50+种音色。能克隆真实声音、生成逼真配音、有声书朗读。',
        'murf'            => 'Murf AI是专业AI配音平台，提供120+种自然音色，支持多语言配音。',
        'fireflies'       => 'Fireflies.ai是AI会议助手，自动录制、转录、总结会议内容。支持Google Meet、Zoom等平台集成。',
        'tactiq'          => 'Tactiq是实时AI会议转录工具，在Google Meet、Zoom、Teams上即时生成字幕和会议摘要。',
        'descript'        => 'Descript是AI音视频编辑器，支持文字编辑视频、语音克隆、声音去除等创新功能。',
        'notion'          => 'Notion是AI增强的知识管理和协作平台，支持笔记、文档、数据库、Wiki等功能。内置AI助手支持内容生成和优化。',
        'notebooklm'      => 'Google NotebookLM是AI学习助手，上传文档后自动生成摘要、问答、播客风格的音频讨论。',
        'mem'             => 'Mem是AI驱动的知识管理工具，自动整理和组织笔记内容，支持与团队共享。',
        'craft'           => 'Craft是优雅的AI笔记应用，支持Markdown、富媒体笔记，AI功能辅助写作和整理。',
        'tome'            => 'Tome是AI演示文稿生成器，输入描述即可创建PPT风格的演示文稿，支持嵌入视频和3D内容。',
        'gamma'           => 'Gamma是AI PPT生成平台，输入主题即可生成完整演示文稿，支持实时协作和精美模板。',
        'beautiful-ai'    => 'Beautiful.ai是AI演示文稿工具，自动设计布局确保幻灯片美观专业，无需设计基础。',
        'wps-ai'          => 'WPS AI是金山办公推出的AI助手，集成于WPS Office，支持文档生成、内容润色、PPT创建等功能。',
        'shimo'           => '石墨文档是协作文档平台，集成AI能力支持内容生成、翻译、总结等功能。',
        'wondercraft'     => 'Wondercraft AI是AI播客制作平台，支持多语言配音和播客内容生成，快速创建音频节目。',
        'tl-dr'           => 'TLDR This是在线文章摘要工具，输入网址即可获得简洁的中文摘要，省去阅读长文时间。',
        'monica'          => 'Monica是AI浏览器助手，基于GPT-4在网页端提供聊天、写作、翻译、总结等能力。支持Chrome扩展。',
        'extend'          => 'ExtendOffice是AI办公插件，深度集成于Word、Excel、PPT，提供内容生成和格式优化。',
        'biling'          => '笔灵AI是面向学生和职场人士的AI写作工具，专注论文、公文、报告等学术和商务文档写作。',
        '秘塔'            => '秘塔AI搜索是无广告、直达结果的AI搜索引擎，支持全网搜索和学术搜索，简洁高效。',
        'trae'            => 'Trae是字节跳动推出的AI编程工具，集成GPT-4和Claude 3.5，支持代码生成、补全、解释，免费使用。',
        'duolingo'        => 'Duolingo是全球最流行的语言学习平台，AI个性化推荐学习路径，游戏化设计让学习更轻松。',
        'coursera'        => 'Coursera是全球领先的在线学习平台，与顶尖大学和企业合作提供AI、数据科学等课程，AI辅助学习路径推荐。',
        'originality'     => 'Originality.ai是AI内容检测工具，能够识别文本是否由ChatGPT、GPT-4等AI生成。内容创作者和编辑必备。',
        'content-at-scale'=> 'Content at Scale是AI SEO内容生成平台，输入关键词即可生成长篇高质量SEO文章。',
        'copy-ai'         => 'Copy.ai是AI文案生成工具，覆盖广告文案、社交媒体、产品描述等场景。',
        'jasper'          => 'Jasper是知名AI营销内容平台，支持品牌声音定制、多语言内容生成，营销团队广泛使用。',
        'writesonic'      => 'Writesonic是AI写作平台，Chatsonic支持实时网络搜索写作，Article Writer生成长篇文章。',
        'copywritely'     => 'Copywritely是AI SEO文案优化工具，检查重复内容、关键词密度、可读性，提升文章搜索引擎排名。',
        'remove-bg'       => 'Remove.bg是AI一键抠图工具，上传图片自动去除背景，无需手动PS处理。',
        'photoroom'       => 'PhotoRoom是AI图像编辑App，支持背景替换、产品图生成、批量编辑，电商卖家必备。',
        'cleanup'         => 'CleanUp pictures是AI修图工具，能智能去除图片中的不需要的物体、文字或人物。',
        'palette'         => 'Palette.fm是AI黑白照片上色工具，上传黑白照片自动生成自然彩色版本。',
        'framer'          => 'Framer是AI网站构建平台，输入描述即可生成完整网站，支持AI文案生成和设计。',
        'locofy'          => 'Locofy是AI设计转代码工具，将Figma设计稿自动转换为生产级React代码。',
        'uizard'          => 'Uizard是AI UI设计工具，支持手绘草图转设计稿、文字描述生成界面、Figma导入。',
        'bing'            => 'Microsoft Bing（新必应）集成ChatGPT能力，支持AI对话搜索，返回带来源的答案。',
        'character-ai'    => 'Character.AI是AI角色扮演平台，用户可创建和对话各种虚拟人物、明星、历史人物，高度拟人化体验。',
        'pi'              => 'Pi是Inflection AI推出的个人AI助手，主打温暖、关心的对话风格，适合日常陪伴和情感支持。',
        'leon'            => 'Leon是AI个人助手，支持自然语言执行任务、回答问题、管理日程。',
        'huayuan'         => '百小应是中国人工智能百强企业开发的AI助手，专注企业级应用场景。',
        'tongdaxin'       => '通答AI是面向企业的AI助手，支持文档处理、数据分析、客服自动化等场景。',
        'lingxi'          => '灵犀AI助手集成抖音和字节内容生态，擅长信息推荐和内容创作辅助。',
        'huoshan'         => '火山引擎是字节跳动云服务品牌，提供大模型API、算力基础设施和行业解决方案。',
        'abab'            => 'ABAB是MiniMax推出的AI大模型，支持高质量对话和内容生成，以稳定的API服务和合理的定价受到开发者欢迎。',
        'metamate'        => 'Metamate是企业级AI助手，支持企业知识库问答、数据分析和业务流程自动化。',
        'bing-ai'         => 'Microsoft Bing AI（新必应）集成ChatGPT能力，支持AI对话搜索，返回带来源的答案。',
        'youchat'         => 'You.com AI Chat是You.com搜索平台的AI对话界面，实时检索网络并生成答案。',
    ];
}
