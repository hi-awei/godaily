<?php
/**
 * aidh_import.php - aidh.cn工具批量导入 (根目录版本)
 * 访问: https://993899.com/aidh_import.php?key=godaily2026&do=1
 * 需要admin session: 先访问 /admin/ 登录，然后带上 cookie 请求本脚本
 */

// 检查session（admin登录后才有权限）
session_start();
$isAdmin = !empty($_SESSION['admin_id']);

// 也支持URL key验证
$KEY = 'godaily2026';
$keyOk = isset($_GET['key']) && $_GET['key'] === $KEY;

if (!$isAdmin && !$keyOk) {
    http_response_code(403);
    echo "Access denied. Login at /admin/ or use ?key=godaily2026";
    exit;
}

if (!isset($_GET['do'])) {
    echo "GoDaily Aidh Import\n";
    echo "Use ?do=1 to execute\n";
    echo "Use ?stats=1 for stats\n";
    exit;
}

if (isset($_GET['stats'])) {
    require_once __DIR__ . '/includes/db.php';
    $db = db();
    $tools = $db->query("SELECT COUNT(*) as c FROM tools")->fetch()['c'];
    $news = $db->query("SELECT COUNT(*) as c FROM news")->fetch()['c'];
    echo "Tools: $tools\nNews: $news\n";
    exit;
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$db = db();
$db->set_charset('utf8mb4');

$catIds = ['ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,'llm'=>10,'ai-coding'=>11,'ai-misc'=>12];
$now = date('Y-m-d H:i:s');
$ok = $dup = $err = 0;
$usedSlugs = [];

$jsonFile = __DIR__ . '/aidh_tools.json';
if (!file_exists($jsonFile)) {
    echo "Error: aidh_tools.json not found\n";
    exit;
}

$json = json_decode(file_get_contents($jsonFile), true);
if (!$json || empty($json['collected'])) {
    echo "Error: No data in JSON\n";
    exit;
}

$total = count($json['collected']);
echo "Importing $total tools...\n";

foreach ($json['collected'] as $idx => $t) {
    $name = $db->real_escape_string(mb_substr(preg_replace('/\s+/',' ',$t['name']??''),0,200));
    $slug = preg_replace('/[^a-z0-9\u4e00-\u9fff-]/u','-', strtolower($t['name']??''));
    $slug = preg_replace('/-+/','-', trim($slug,'-'));
    $slug = mb_substr($slug,0,60);
    $bs = $slug; $i=1;
    while (in_array($slug,$usedSlugs)) $slug=$bs.'-'.$i++;
    $usedSlugs[]=$slug;
    $tagline = $db->real_escape_string(mb_substr($t['tagline']??'',0,300));
    $desc = $db->real_escape_string(mb_substr($t['description']??'',0,2000));
    $icon = $db->real_escape_string(mb_substr($t['icon_url']??'',0,500));
    $tags = $db->real_escape_string(mb_substr($t['tags']??'',0,500));
    $src = $db->real_escape_string($t['source_url']??'');
    $catId = $catIds[$t['category']] ?? 12;
    $sql = "INSERT INTO tools (name,slug,tagline,description,url,icon_url,category_id,tags,status,source,source_url,created_at,updated_at) VALUES ('$name','$slug','$tagline','$desc','','$icon',$catId,'$tags','published','aidh.cn','$src','$now','$now') ON DUPLICATE KEY UPDATE updated_at='$now'";
    if ($db->query($sql)) {
        if ($db->affected_rows > 0) $ok++; else $dup++;
    } else { $err++; }
    if (($idx+1) % 50 === 0) { echo ($idx+1)."/$total ($ok OK)\n"; }
}

echo "DONE OK:$ok Dup:$dup Err:$err\n";
$db->close();
