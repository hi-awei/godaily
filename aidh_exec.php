<?php
// Disable all output compression
if (function_exists('apache_setenv')) apache_setenv('no-gzip', '1');
ini_set('zlib.output_compression', 'Off');
ini_set('output_handler', '');
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Encoding: identity');
header('X-Accel-Buffering: no');

define('SECRET_KEY', 'godaily2026');
if (!isset($_GET['key']) || $_GET['key'] !== SECRET_KEY) { http_response_code(403); exit('403'); }
if (!isset($_GET['do'])) { echo "Ready.\n"; exit; }

$db = @new mysqli('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com');
if ($db->connect_error) { echo "DB Error\n"; exit; }
$db->set_charset('utf8mb4');

$catIds = ['ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,'llm'=>10,'ai-coding'=>11,'ai-misc'=>12];
$now = date('Y-m-d H:i:s');
$ok = $dup = $err = 0;
$usedSlugs = [];
$json = json_decode(file_get_contents(__DIR__ . '/aidh_tools.json'), true);
if (!$json || empty($json['collected'])) { echo "No data\n"; exit; }

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
    $url = $db->real_escape_string(mb_substr($t['url']??'',0,500));
    $icon = $db->real_escape_string(mb_substr($t['icon_url']??'',0,500));
    $tags = $db->real_escape_string(mb_substr($t['tags']??'',0,500));
    $src = $db->real_escape_string($t['source_url']??'');
    $catId = $catIds[$t['category']] ?? 12;
    $sql = "INSERT INTO tools (name,slug,tagline,description,url,icon_url,category_id,tags,status,source,source_url,created_at,updated_at) VALUES ('$name','$slug','$tagline','$desc','$url','$icon',$catId,'$tags','published','aidh.cn','$src','$now','$now') ON DUPLICATE KEY UPDATE updated_at='$now'";
    if ($db->query($sql)) {
        if ($db->affected_rows > 0) $ok++; else $dup++;
    } else { $err++; }
    if (($idx+1) % 50 === 0) { echo "Progress: $ok OK, $dup Dup, $err Err\n"; }
}

echo "DONE OK:$ok Dup:$dup Err:$err\n";
$db->close();
