<?php
/**
 * aidh_import.php - 通过URL参数触发导入
 * 服务器直接读取JSON并执行
 */
define('SECRET_KEY', 'godaily2026');
if (!isset($_GET['key']) || $_GET['key'] !== SECRET_KEY) {
    http_response_code(403);
    die('Access denied');
}
if (!isset($_GET['do'])) {
    echo '<html><head><meta charset="UTF-8"><title>导入工具</title></head><body>';
    echo '<h1>📥 aidh.cn 工具导入</h1>';
    echo '<p>将通过服务器直接读取JSON并导入数据库。</p>';
    echo '<form method="get"><input type="hidden" name="key" value="godaily2026"><input type="hidden" name="do" value="1"><input type="submit" value="🚀 开始导入"></form>';
    echo '</body></html>';
    exit;
}

// Import directly from JSON file in same dir
$jsonFile = __DIR__ . '/aidh_tools.json';
if (!file_exists($jsonFile)) {
    echo "❌ JSON文件不存在: $jsonFile\n";
    echo "当前目录: " . __DIR__ . "\n";
    exit;
}
$json = json_decode(file_get_contents($jsonFile), true);
if (!$json || empty($json['collected'])) {
    echo "❌ JSON格式错误或无数据";
    exit;
}
$db = @new mysqli('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com');
if ($db->connect_error) {
    echo "❌ DB错误: " . $db->connect_error;
    exit;
}
$db->set_charset('utf8mb4');
$catIds = ['ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,'llm'=>10,'ai-coding'=>11,'ai-misc'=>12];
$now = date('Y-m-d H:i:s');
$ok = $dup = $err = 0;
$usedSlugs = [];
$total = count($json['collected']);
echo "开始导入 $total 个工具...\n";
ob_flush(); flush();
foreach ($json['collected'] as $t) {
    $name = $db->real_escape_string(mb_substr(preg_replace('/\s+/',' ',$t['name']??''),0,200));
    $slug = preg_replace('/[^a-z0-9\u4e00-\u9fff-]/u','-', strtolower($t['name']??''));
    $slug = preg_replace('/-+/','-', trim($slug,'-'));
    $slug = mb_substr($slug,0,60);
    $bs = $slug; $i=1;
    while (in_array($slug,$usedSlugs)) $slug=$bs.'-'.$i++;
    $usedSlugs[]=$slug;
    $tagline = $db->real_escape_string(mb_substr($t['tagline']??'',0,300));
    $desc = $db->real_escape_string(mb_substr($t['description']??'',0,2000));
    $url = $db->real_escape_string(mb_substr($t['url']??'',0,500));
    $icon = $db->real_escape_string(mb_substr($t['icon_url']??'',0,500));
    $tags = $db->real_escape_string(mb_substr($t['tags']??'',0,500));
    $src = $db->real_escape_string($t['source_url']??'');
    $catId = $catIds[$t['category']] ?? 12;
    $sql = "INSERT INTO tools (name,slug,tagline,description,url,icon_url,category_id,tags,status,source,source_url,created_at,updated_at) VALUES ('$name','$slug','$tagline','$desc','$url','$icon',$catId,'$tags','published','aidh.cn','$src','$now','$now') ON DUPLICATE KEY UPDATE updated_at='$now'";
    if ($db->query($sql)) {
        if ($db->affected_rows > 0) $ok++; else $dup++;
    } else {
        $err++;
        if ($err <= 3) echo "Error: ".$db->error."\n";
    }
    if ($ok % 50 === 0 && $ok > 0) { echo "进度: $ok 新增, $dup 重复, $err 错误\n"; ob_flush(); flush(); }
}
echo "\n✅ 完成！新增 $ok 条, 重复 $dup 条, 错误 $err 条\n";
echo "<p><a href='/admin/tools.php'>查看工具列表 →</a></p>";
$db->close();
