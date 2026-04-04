<?php
// test.php - 输出纯 ASCII，无中文，无 gzip
echo "TEST OK: " . date('H:i:s') . "\n";
echo "DB: " . (function_exists('mysqli_connect') ? 'mysqli_available' : 'no_mysqli') . "\n";
