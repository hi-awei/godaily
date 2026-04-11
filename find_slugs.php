<?php
// find_slugs.php - 查找工具slug
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: text/plain; charset=utf-8');
$key = $_GET['key'] ?? '';
if ($key !== 'godaily2026') { echo "Unauthorized"; exit; }

$db = new PDO('mysql:host=localhost;dbname=web01_com;charset=utf8mb4', 'web01_com', '3FT7Ppatfp19XbAh', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT]);

$names = ['Anthropic','OpenAI','Lobe','Firecrawl','firecrawl-ai'];
foreach ($names as $name) {
    $stmt = $db->prepare("SELECT id, slug, name, icon FROM tools WHERE name LIKE ? LIMIT 5");
    $stmt->execute(['%' . $name . '%']);
    $rows = $stmt->fetchAll();
    if ($rows) {
        foreach ($rows as $r) {
            echo "FOUND: id={$r['id']} slug={$r['slug']} name={$r['name']} icon={$r['icon']}\n";
        }
    } else {
        echo "NOT FOUND: $name\n";
    }
}

// Also show all slugs that have an empty icon
$empty = $db->query("SELECT id, slug, name FROM tools WHERE (icon IS NULL OR icon='') AND status=1 LIMIT 20");
echo "\nTools with empty icon:\n";
foreach ($empty->fetchAll() as $r) {
    echo "  {$r['slug']} / {$r['name']}\n";
}
?>
