<?php
// 创建友链表
$host = '127.0.0.1';
$dbname = 'web01_com';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    // 创建友链表
    $db->exec("CREATE TABLE IF NOT EXISTS friend_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        url VARCHAR(255) NOT NULL,
        sort_order INT DEFAULT 0,
        status TINYINT DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // 插入默认友链
    $existing = $db->query("SELECT COUNT(*) FROM friend_links")->fetchColumn();
    if ($existing == 0) {
        $links = [
            ['AIHeros', 'https://www.aiheros.cn', 1],
            ['AI工具集', 'https://www.aihelper.design', 2],
            ['AI导航网', 'https://www.aidh.cn', 3],
            ['未来AI工具', 'https://futureaitools.com', 4],
        ];
        foreach ($links as $l) {
            $db->prepare("INSERT INTO friend_links (name, url, sort_order) VALUES (?, ?, ?)")->execute($l);
        }
    }
    
    echo "done";
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}