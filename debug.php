<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Test</h1>";

// Test config
echo "<p>Testing config...</p>";
$cfg = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
echo "Config path: $cfg<br>";
echo "Exists: " . (file_exists($cfg) ? 'YES' : 'NO') . "<br>";

require_once $cfg;
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";

// Test db
echo "<p>Testing db...</p>";
$cfg_file2 = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
echo "db.php path: $cfg_file2<br>";
echo "Exists: " . (file_exists($cfg_file2) ? 'YES' : 'NO') . "<br>";

require_once $cfg_file2;
echo "db() function exists: " . (function_exists('db') ? 'YES' : 'NO') . "<br>";

try {
    $dbh = db();
    echo "Database connected: YES<br>";
} catch (Exception $e) {
    echo "Database ERROR: " . $e->getMessage() . "<br>";
}

// Test functions
echo "<p>Testing functions...</p>";
$func_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';
echo "functions.php exists: " . (file_exists($func_file) ? 'YES' : 'NO') . "<br>";

require_once $func_file;
echo "clean() exists: " . (function_exists('clean') ? 'YES' : 'NO') . "<br>";
echo "category_color() exists: " . (function_exists('category_color') ? 'YES' : 'NO') . "<br>";

// Test header
echo "<p>Testing header...</p>";
$hdr_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'header.php';
echo "header.php exists: " . (file_exists($hdr_file) ? 'YES' : 'NO') . "<br>";

echo "<p>ALL TESTS DONE</p>";
?>
