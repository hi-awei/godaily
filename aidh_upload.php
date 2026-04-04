<?php
/**
 * aidh_import.php - 导入 aidh.cn 采集的工具数据
 * 访问: https://993899.com/admin/aidh_import.php?key=godaily2026
 */

define('SECRET_KEY', 'godaily2026');

if (!isset($_GET['key']) || $_GET['key'] !== SECRET_KEY) {
    http_response_code(403);
    die('Access denied');
}

$jsonFile = __DIR__ . '/../aidh_tools.json';
if (!file_exists($jsonFile)) {
    die('数据文件不存在: ' . $jsonFile);
}

$json = json_decode(file_get_contents($jsonFile), true);
if (!$json || empty($json['collected'])) {
    die('无数据或数据格式错误');
}

$tools = $json['collected'];
$count = count($tools);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>导入工具 - GoDaily</title>";
echo "<style>body{font-family:-apple-system,sans-serif;padding:40px;background:#f8fafc;} ";
echo ".ok{color:#22c55e;font-weight:700;} .err{color:#ef4444;} ";
echo ".box{background:white;border-radius:8px;padding:24px;max-width:600px;margin:40px auto;box-shadow:0 1px 3px rgba(0,0,0,0.1);} ";
echo "table{width:100%;border-collapse:collapse;margin-top:16px;} ";
echo "th,td{padding:8px 12px;text-align:left;border-bottom:1px solid #e2e8f0;} th{background:#f1f5f9;} ";
echo ".btn{display:inline-block;background:#3b82f6;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;margin:4px;} ";
echo ".btn:hover{background:#2563eb;} .btn-red{background:#ef4444;} .btn-red:hover{background:#dc2626;} ";
echo "input[type='submit']{background:#22c55e;color:white;padding:10px 24px;border:none;border-radius:6px;cursor:pointer;font-size:16px;} ";
echo "input[type='submit']:hover{background:#16a34a;} ";
echo "input[type='submit'].dry{background:#f59e0b;} ";
echo ".cat{font-size:13px;color:#64748b;}</style></head><body>";
echo "<h1>📥 aidh.cn 工具导入</h1>";
echo "<div class='box'>";
echo "<p>找到 <strong>$count</strong> 个工具</p>";

// Category stats
$cats = [];
foreach ($tools as $t) {
    $c = $t['category'] ?? 'ai-misc';
    $cats[$c] = ($cats[$c] ?? 0) + 1;
}
echo "<table><tr><th>分类</th><th>数量</th></tr>";
foreach ($cats as $cat => $n) echo "<tr><td>$cat</td><td>$n</td></tr>";
echo "</table>";
echo "</div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actually import
    $db = @new mysqli('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com');
    if ($db->connect_error) {
        echo "<p class='err'>数据库连接失败: " . $db->connect_error . "</p>";
    } else {
        $db->set_charset('utf8mb4');
        $ok = 0; $dup = 0; $err = 0;
        $usedSlugs = [];
        
        $catIds = [
            'ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,
            'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,
            'llm'=>10,'ai-coding'=>11,'ai-misc'=>12,
        ];
        
        $now = date('Y-m-d H:i:s');
        $dry = isset($_POST['dry']);
        
        foreach ($tools as $t) {
            $name = $db->real_escape_string(substr($t['name'] ?? '', 0, 200));
            $slug = preg_replace('/[^a-z0-9\u4e00-\u9fff-]/u', '-', strtolower($t['name'] ?? ''));
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            $slug = substr($slug, 0, 60);
            $baseSlug = $slug;
            $i = 1;
            while (in_array($slug, $usedSlugs)) { $slug = $baseSlug . '-' . $i++; }
            $usedSlugs[] = $slug;
            
            $tagline = $db->real_escape_string(substr($t['tagline'] ?? '', 0, 300));
            $desc = $db->real_escape_string(substr($t['description'] ?? '', 0, 2000));
            $url = $db->real_escape_string(substr($t['url'] ?? '', 0, 500));
            $icon = $db->real_escape_string(substr($t['icon_url'] ?? '', 0, 500));
            $tags = $db->real_escape_string(substr($t['tags'] ?? '', 0, 500));
            $src = $db->real_escape_string($t['source_url'] ?? '');
            $catId = $catIds[$t['category']] ?? 12;
            
            $sql = "INSERT INTO tools (name,slug,tagline,description,url,icon_url,category_id,tags,status,source,source_url,created_at,updated_at) VALUES ('$name','$slug','$tagline','$desc','$url','$icon',$catId,'$tags','published','aidh.cn','$src','$now','$now') ON DUPLICATE KEY UPDATE updated_at='$now'";
            
            if ($dry) {
                echo "<p>DRY: $sql</p>";
                $dup++;
            } else {
                if ($db->query($sql)) {
                    if ($db->affected_rows > 0) $ok++; else $dup++;
                } else {
                    $err++;
                    echo "<p class='err'>Error: " . $db->error . " | SQL: $sql</p>";
                }
            }
        }
        
        if (!$dry) {
            echo "<div class='box'>";
            echo "<h2 class='ok'>导入完成！</h2>";
            echo "<p>✅ 新增: $ok</p>";
            echo "<p>⏭️  重复: $dup</p>";
            echo "<p class='err'>❌ 错误: $err</p>";
            echo "<p><a href='/admin/tools.php' class='btn'>查看工具列表 →</a></p>";
            echo "</div>";
        } else {
            echo "<p>DRY RUN: 模拟导入 $count 条（无实际写入）</p>";
        }
        $db->close();
    }
} else {
    echo "<div class='box'>";
    echo "<h2>确认导入</h2>";
    echo "<p>将从 aidh_tools.json 导入 <strong>$count</strong> 个工具到数据库</p>";
    echo "<p>重复名称会自动跳过（ON DUPLICATE KEY UPDATE）</p>";
    echo "<form method='post'>";
    echo "<input type='submit' name='dry' value='🔍 模拟运行（不写入）' class='dry'> ";
    echo "<input type='submit' value='🚀 确认导入'>";
    echo "</form>";
    echo "</div>";
}

echo "</body></html>";
