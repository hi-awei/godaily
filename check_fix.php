<?php header('Content-Type: text/plain; charset=utf-8');
// Read tools.php and check if it contains the fix
$content = file_get_contents('/www/wwwroot/web01.com/tools.php');
if (strpos($content, 'stmtCount') !== false) {
    echo "FIXED: tools.php contains stmtCount (prepared statement for COUNT)\n";
} else {
    echo "NOT FIXED: tools.php still uses query() for COUNT\n";
}
// Show the relevant lines
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'COUNT') !== false || strpos($line, 'stmtCount') !== false || strpos($line, 'total') !== false) {
        echo "L" . ($i+1) . ": " . trim($line) . "\n";
    }
}
