<?php
// batch_ajax.php - admin后台批量导入，每条用AJAX执行
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['admin_id'])) { echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['act']) && $_GET['act'] === 'batch_import') {
    $json = json_decode(file_get_contents(__DIR__ . '/../aidh_tools.json'), true);
    if (!$json || empty($json['collected'])) { echo json_encode(['success'=>false,'message'=>'No data']); exit; }
    $catIds = ['ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,'llm'=>10,'ai-coding'=>11,'ai-misc'=>12];
    $now = date('Y-m-d H:i:s');
    $ok = $dup = $err = 0;
    $usedSlugs = [];
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
        $icon = $db->real_escape_string(mb_substr($t['icon_url']??'',0,500));
        $tags = $db->real_escape_string(mb_substr($t['tags']??'',0,500));
        $src = $db->real_escape_string($t['source_url']??'');
        $catId = $catIds[$t['category']] ?? 12;
        $sql = "INSERT INTO tools (name,slug,tagline,description,url,icon_url,category_id,tags,status,source,source_url,created_at,updated_at) VALUES ('$name','$slug','$tagline','$desc','','$icon',$catId,'$tags','published','aidh.cn','$src','$now','$now') ON DUPLICATE KEY UPDATE updated_at='$now'";
        if ($db->query($sql)) { if ($db->affected_rows > 0) $ok++; else $dup++; } else { $err++; }
    }
    echo json_encode(['success'=>true,'message'=>"OK:$ok Dup:$dup Err:$err"]);
    exit;
}
echo json_encode(['success'=>false,'message'=>'Use ?act=batch_import']);
