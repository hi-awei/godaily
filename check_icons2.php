<?php
require_once 'includes/db.php';
$db = db();

// Check first 5 tools
$tools = $db->query("SELECT id, name, icon FROM tools WHERE status=1 ORDER BY id LIMIT 5")->fetchAll();
echo "First 5 tools:\n";
foreach ($tools as $t) {
    $hasIcon = !empty($t['icon']) ? 'YES' : 'NO';
    echo "  ID={$t['id']} name={$t['name']} icon={$hasIcon}";
    if (!empty($t['icon'])) echo " url=" . substr($t['icon'], 0, 50);
    echo "\n";
}
