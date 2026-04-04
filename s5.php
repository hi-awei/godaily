<?php header('Content-Type: text/plain; charset=utf-8');
echo "START\n";
$host = 'localhost'; $dbname = 'web01_com'; $user = 'web01_com'; $pass = '3FT7Ppatfp19XbAh';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    echo "DB OK\n";
    $q = isset($_GET['q']) ? $_GET['q'] : '';
    $search = htmlspecialchars(trim($q), ENT_QUOTES, 'UTF-8');
    $stmt = $pdo->prepare("SELECT id,name FROM tools WHERE status=1 AND name LIKE ? LIMIT 5");
    $stmt->execute(["%$search%"]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "found=" . count($r) . "\n";
    foreach ($r as $t) echo " {$t['name']}\n";
} catch (Exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}
echo "END";