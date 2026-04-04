<?php
$host = 'localhost';
$user = 'web01_com';
$pass = '3FT7Ppatfp19XbAh';
$dbname = 'web01_com';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE `$dbname`");
    $hash = password_hash('Qclaw2026!', PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE admins SET password=? WHERE username='admin'")->execute([$hash]);
    echo "密码已更新为: Qclaw2026!";
} catch (PDOException $e) {
    echo "失败: " . $e->getMessage();
}
