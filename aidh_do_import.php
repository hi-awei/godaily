<?php
/**
 * aidh_import.php - 直接执行导入（数据内嵌，无需上传）
 * 访问: https://993899.com/admin/aidh_import.php?key=godaily2026
 */
define('SECRET_KEY', 'godaily2026');
if (!isset($_GET['key']) || $_GET['key'] !== SECRET_KEY) {
    http_response_code(403);
    die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['do'])) {
    $db = @new mysqli('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com');
    if ($db->connect_error) {
        echo "DB error: " . $db->connect_error;
        exit;
    }
    $db->set_charset('utf8mb4');
    $catIds = ['ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,'llm'=>10,'ai-coding'=>11,'ai-misc'=>12];
    $now = date('Y-m-d H:i:s');
    $ok = $dup = $err = 0;
    $usedSlugs = [];
    $esc = function($s) use($db) { return $db->real_escape_string(mb_substr(preg_replace('/\s+/',' ',$s??''),0,4000)); };
    $tools = json_decode(file_get_contents(__DIR__.'/../aidh_tools.json'), true);
    if (!$tools || empty($tools['collected'])) { echo "No data"; exit; }
    $total = count($tools['collected']);
    echo "开始导入 $total 个工具...\n";
    foreach ($tools['collected'] as $t) {
        $name = $esc($t['name']??'');
        $slug = preg_replace('/[^a-z0-9\u4e00-\u9fff-]/u','-', strtolower($t['name']??''));
        $slug = preg_replace('/-+/','-', trim($slug,'-'));
        $slug = mb_substr($slug,0,60);
        $bs = $slug; $i=1;
        while (in_array($slug,$usedSlugs)) $slug=$bs.'-'.$i++;
        $usedSlugs[]=$slug;
        $tagline = $esc($t['tagline']??'');
        $desc = $esc($t['description']??'');
        $url = $esc($t['url']??'');
        $icon = $esc($t['icon_url']??'');
        $tags = $esc($t['tags']??'');
        $src = $esc($t['source_url']??'');
        $catId = $catIds[$t['category']] ?? 12;
        $sql = "INSERT INTO tools (name,slug,tagline,description,url,icon_url,category_id,tags,status,source,source_url,created_at,updated_at) VALUES ('$name','$slug','$tagline','$desc','$url','$icon',$catId,'$tags','published','aidh.cn','$src','$now','$now') ON DUPLICATE KEY UPDATE updated_at='$now'";
        if ($db->query($sql)) {
            if ($db->affected_rows > 0) $ok++; else $dup++;
        } else {
            $err++;
            if ($err <= 3) echo "Err: ".$db->error."\n";
        }
        if ($ok % 50 === 0) echo "进度: $ok 新增, $dup 重复, $err 错误\n";
    }
    echo "\n完成！新增 $ok 条, 重复 $dup 条, 错误 $err 条\n";
    $db->close();
} else {
    echo '<form method="get"><input type="hidden" name="key" value="godaily2026"><input type="hidden" name="do" value="1"><input type="submit" value="🚀 开始导入"></form>';
}
