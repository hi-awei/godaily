<?php
// test.php - always returns ASCII, no gzip issue
echo "TEST: " . date('H:i:s') . "\n";
echo "DB: " . (@mysqli_connect('localhost', 'web01_com', '3FT7Ppatfp19XbAh', 'web01_com') ? 'OK' : 'FAIL') . "\n";
echo "JSON: " . (file_exists(__DIR__ . '/aidh_tools.json') ? 'OK' : 'MISSING') . "\n";
