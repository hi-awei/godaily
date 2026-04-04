<?php
require_once 'includes/db.php';
$db = db();

// Check the actual tools.php source for icon-related code
$source = file_get_contents('tools.php');
if (strpos($source, 'tool-icon-img') !== false) {
    echo "tools.php HAS icon display code (tool-icon-img class found)\n";
} else {
    echo "tools.php MISSING icon display code!\n";
}

if (strpos($source, 'tool[') !== false) {
    echo "tools.php HAS tool data array\n";
} else {
    echo "tools.php MISSING tool data array!\n";
}

// Check first 3 tools icon field
$tools = $db->query("SELECT id, name, icon FROM tools WHERE status=1 ORDER BY id LIMIT 5")->fetchAll();
echo "\nFirst 5 tools:\n";
foreach ($tools as $t) {
    $hasIcon = !empty($t['icon']) ? 'YES' : 'NO';
    echo "  ID={$t['id']} name={$t['name']} icon={$hasIcon}";
    if ($hasIcon) echo " url={$t['icon']}";
    echo "\n";
}
