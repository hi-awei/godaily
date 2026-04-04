<?php
// fix_action.php - 修复 admin/action.php 的 parse error
if (!isset($_GET['key']) || $_GET['key'] !== 'godaily2026') { http_response_code(403); exit; }

$newContent = <<<'PHP'
<?php
/**
 * 管理后台 AJAX 操作
 */
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = db();
$action = $_GET['act'] ?? $_GET['action'] ?? '';
$result = ['success' => false, 'message' => 'Unknown action'];

switch ($action) {
    case 'approve_sub': {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $sub = $db->query("SELECT * FROM submissions WHERE id=$id")->fetch();
            if ($sub) {
                $slug = slugify($sub['name']) . '-' . substr(md5($sub['name']), 0, 6);
                $stmt = $db->prepare("INSERT INTO tools (name, slug, tagline, description, url, category, pricing, status, vote_count, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0, NOW())");
                $stmt->execute([$sub['name'], $slug, $sub['description'], $sub['description'], $sub['url'], $sub['category'], $sub['pricing']]);
                $db->query("UPDATE submissions SET status=1 WHERE id=$id");
                $result = ['success' => true, 'message' => 'Approved'];
            } else {
                $result = ['success' => false, 'message' => 'Not found'];
            }
        }
        break;
    }
    case 'reject_sub': {
        $id = intval($_GET['id'] ?? 0);
        $newStatus = isset($_GET['status']) ? intval($_GET['status']) : -1;
        if ($id > 0) {
            if ($newStatus === 0) {
                $db->query("UPDATE submissions SET status=0 WHERE id=$id");
                $result = ['success' => true, 'message' => 'Reset'];
            } else {
                $db->query("UPDATE submissions SET status=-1 WHERE id=$id");
                $result = ['success' => true, 'message' => 'Rejected'];
            }
        }
        break;
    }
    case 'delete_tool': {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $db->query("DELETE FROM tools WHERE id=$id");
            $result = ['success' => true, 'message' => 'Deleted'];
        }
        break;
    }
    case 'toggle_featured': {
        $id = intval($_GET['id'] ?? 0);
        $val = isset($_GET['val']) ? intval($_GET['val']) : -1;
        if ($id > 0) {
            if ($val !== -1) {
                $db->query("UPDATE tools SET featured=$val WHERE id=$id");
            } else {
                $db->query("UPDATE tools SET featured=NOT featured WHERE id=$id");
            }
            $result = ['success' => true, 'message' => 'Toggled'];
        }
        break;
    }
    case 'toggle_hot': {
        $id = intval($_GET['id'] ?? 0);
        $val = isset($_GET['val']) ? intval($_GET['val']) : -1;
        $type = $_GET['type'] ?? 'tool';
        if ($id > 0) {
            $table = $type === 'news' ? 'news' : 'tools';
            $field = 'is_hot';
            if ($val !== -1) {
                $db->query("UPDATE $table SET $field=$val WHERE id=$id");
            } else {
                $db->query("UPDATE $table SET $field=NOT $field WHERE id=$id");
            }
            $result = ['success' => true, 'message' => 'Toggled'];
        }
        break;
    }
    case 'delete_news': {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $db->query("DELETE FROM news WHERE id=$id");
            $result = ['success' => true, 'message' => 'Deleted'];
        }
        break;
    }
    case 'stats': {
        $toolCount = $db->query("SELECT COUNT(*) as c FROM tools")->fetch()['c'];
        $newsCount = $db->query("SELECT COUNT(*) as c FROM news")->fetch()['c'];
        $subCount = $db->query("SELECT COUNT(*) as c FROM submissions WHERE status=0")->fetch()['c'];
        $result = ['success' => true, 'data' => ['tools' => $toolCount, 'news' => $newsCount, 'submissions' => $subCount]];
        break;
    }
    case 'batch_import': {
        $jsonFile = __DIR__ . '/../aidh_tools.json';
        if (!file_exists($jsonFile)) { $result = ['success'=>false,'message'=>'JSON not found']; break; }
        $json = json_decode(file_get_contents($jsonFile), true);
        if (!$json || empty($json['collected'])) { $result=['success'=>false,'message'=>'No data']; break; }
        $catIds = ['ai-chatbots'=>2,'ai-writing'=>3,'ai-image'=>4,'ai-video'=>5,'ai-audio'=>6,'productivity'=>7,'design'=>8,'academic'=>9,'llm'=>10,'ai-coding'=>11,'ai-misc'=>12];
        $now = date('Y-m-d H:i:s');
        $ok = $dup = $err = 0;
        $usedSlugs = [];
        foreach ($json['collected'] as $t) {
            $name = $db->real_escape_string(mb_substr(preg_replace('/\s+/',' ',$t['name']??''),0,200));
            $slug = preg_replace('/[^a-z0-9\x{4e00}-\x{9fff}-]/u','-', mb_strtolower($t['name']??''));
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
        $result = ['success'=>true,'message'=>"OK:$ok Dup:$dup Err:$err"];
        break;
    }
    default:
        $result = ['success' => false, 'message' => 'Unknown: ' . $action];
}

echo json_encode($result);
PHP;

$targetFile = __DIR__ . '/admin/action.php';
$ok = file_put_contents($targetFile, $newContent);
if ($ok !== false) {
    echo "SUCCESS: admin/action.php fixed ($ok bytes)";
} else {
    echo "FAILED: could not write to admin/action.php";
}
