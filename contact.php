<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = '联系我们';
$currentPage = 'contact';
$pageDesc = '联系AI导航团队 - 提交建议、反馈问题或商务合作。';
$pageKeywords = '联系AI导航,商务合作,意见反馈,AI工具推荐';
require_once __DIR__ . '/templates/header.php';
?>

<div class="container" style="max-width:900px;margin:0 auto;padding:40px 20px;">
    <h1 style="font-size:28px;font-weight:700;margin-bottom:30px;color:var(--text-primary);">联系我们</h1>

    <div class="contact-section" style="background:var(--card-bg);border-radius:12px;padding:32px;margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:20px;color:var(--text-primary);">📬 联系方式</h2>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;">
            <div style="padding:24px;background:var(--bg);border-radius:8px;">
                <div style="font-size:24px;margin-bottom:12px;">📧</div>
                <h3 style="font-size:16px;font-weight:600;margin-bottom:8px;color:var(--text-primary);">电子邮件</h3>
                <p style="color:var(--text-secondary);font-size:15px;line-height:1.6;">
                    通用咨询：<a href="mailto:contact@993899.com" style="color:var(--primary);">contact@993899.com</a><br>
                    工具推荐：<a href="mailto:contact@993899.com" style="color:var(--primary);">contact@993899.com</a><br>
                    <span style="font-size:13px;color:var(--text-secondary);">我们会在 1-3 个工作日内回复</span>
                </p>
            </div>
            
            <div style="padding:24px;background:var(--bg);border-radius:8px;">
                <div style="font-size:24px;margin-bottom:12px;">🌐</div>
                <h3 style="font-size:16px;font-weight:600;margin-bottom:8px;color:var(--text-primary);">在线渠道</h3>
                <p style="color:var(--text-secondary);font-size:15px;line-height:1.6;">
                    提交工具：<a href="/submit.php" style="color:var(--primary);">在线提交</a><br>
                    GitHub：<a href="https://github.com/hi-awei/AI导航" target="_blank" style="color:var(--primary);">hi-awei/AI导航</a><br>
                    <span style="font-size:13px;color:var(--text-secondary);">欢迎提交 Issue 或 PR</span>
                </p>
            </div>

            <div style="padding:24px;background:var(--bg);border-radius:8px;">
                <div style="font-size:24px;margin-bottom:12px;">🏢</div>
                <h3 style="font-size:16px;font-weight:600;margin-bottom:8px;color:var(--text-primary);">网站信息</h3>
                <p style="color:var(--text-secondary);font-size:15px;line-height:1.6;">
                    网站名称：AI导航（轻松一百）<br>
                    网站域名：www.993899.com<br>
                    备案号：<a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow" style="color:var(--primary);">宁ICP备2024005816号</a>
                </p>
            </div>
        </div>
    </div>

    <div class="contact-section" style="background:var(--card-bg);border-radius:12px;padding:32px;margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:20px;color:var(--text-primary);">💼 商务合作</h2>
        <p style="line-height:1.8;color:var(--text-secondary);font-size:16px;">
            如果您是 AI 工具的开发者或团队，希望将您的产品收录到我们的导航站，或者有其他商务合作意向，欢迎通过邮件联系我们。我们提供：
        </p>
        <ul style="line-height:2;color:var(--text-secondary);font-size:16px;padding-left:20px;margin-top:12px;">
            <li>工具收录：免费收录优质 AI 工具</li>
            <li>内容合作：教程撰写、联合推广</li>
            <li>广告投放：网站广告位合作</li>
        </ul>
    </div>

    <div class="contact-section" style="background:var(--card-bg);border-radius:12px;padding:32px;">
        <h2 style="font-size:22px;font-weight:600;margin-bottom:20px;color:var(--text-primary);">❓ 常见问题</h2>
        
        <div style="margin-bottom:16px;padding:16px;background:var(--bg);border-radius:8px;">
            <h3 style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">如何提交一款 AI 工具？</h3>
            <p style="color:var(--text-secondary);font-size:15px;">请访问我们的<a href="/submit.php" style="color:var(--primary);">提交工具</a>页面，填写工具名称、网址和简要描述即可。我们会在 24 小时内审核。</p>
        </div>
        
        <div style="margin-bottom:16px;padding:16px;background:var(--bg);border-radius:8px;">
            <h3 style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">工具信息有误怎么办？</h3>
            <p style="color:var(--text-secondary);font-size:15px;">如果发现工具信息不准确或已过时，请通过邮件 <a href="mailto:contact@993899.com" style="color:var(--primary);">contact@993899.com</a> 反馈，我们会及时更正。</p>
        </div>
        
        <div style="padding:16px;background:var(--bg);border-radius:8px;">
            <h3 style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">收录标准是什么？</h3>
            <p style="color:var(--text-secondary);font-size:15px;">我们收录有实际使用价值、可正常访问的 AI 工具。工具需有明确的官网或下载渠道，且不包含违法违规内容。</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
