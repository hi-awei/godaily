<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = '提交工具';
$currentPage = 'submit';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? clean($_POST['name']) : '';
    $url = isset($_POST['url']) ? clean($_POST['url']) : '';
    $email = isset($_POST['email']) ? clean($_POST['email']) : '';
    $description = isset($_POST['description']) ? clean($_POST['description']) : '';
    $category = isset($_POST['category']) ? clean($_POST['category']) : 'other';
    $pricing = isset($_POST['pricing']) ? clean($_POST['pricing']) : 'unknown';

    if (empty($name) || empty($url)) {
        $error = '请填写工具名称和网址';
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = '请填写正确的网址';
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO submissions (name, url, email, description, category, pricing, ip) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($name, $url, $email, $description, $category, $pricing, get_client_ip()));
            $success = true;
        } catch (Exception $e) {
            $error = '提交失败，请稍后重试';
        }
    }
}

require_once 'templates/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">📬 提交AI工具</h1>
        <p class="page-desc">有你喜欢的工具我们还没收录？填写下面的表单，我们会认真审核并在24小时内处理</p>
    </div>

    <?php if ($success): ?>
        <div class="success-box">
            <h2>✅ 提交成功！</h2>
            <p>感谢你的推荐，我们会在24小时内审核并处理。</p>
            <a href="index.php" class="btn-primary">返回首页</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="submit-form-wrapper">
            <form method="post" class="submit-form">
                <div class="form-group">
                    <label for="name">工具名称 <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required placeholder="例如：ChatGPT">
                </div>
                <div class="form-group">
                    <label for="url">官方网址 <span class="required">*</span></label>
                    <input type="url" id="url" name="url" required placeholder="https://...">
                </div>
                <div class="form-group">
                    <label for="email">邮箱（可选）</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label for="description">工具简介</label>
                    <textarea id="description" name="description" rows="4" placeholder="简单介绍一下这个工具..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">分类</label>
                        <select id="category" name="category">
                            <option value="other">其他</option>
                            <option value="llm">大语言模型</option>
                            <option value="image">AI图像</option>
                            <option value="video">AI视频</option>
                            <option value="audio">AI音频</option>
                            <option value="coding">编程辅助</option>
                            <option value="writing">写作助手</option>
                            <option value="productivity">效率工具</option>
                            <option value="design">设计工具</option>
                            <option value="marketing">营销推广</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pricing">定价方式</label>
                        <select id="pricing" name="pricing">
                            <option value="unknown">未知</option>
                            <option value="free">完全免费</option>
                            <option value="freemium">免费+付费版</option>
                            <option value="paid">付费</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-primary btn-large">提交工具</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
