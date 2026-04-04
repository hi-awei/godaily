<?php
// Clean up all debug/temp files
$files = ['debug_icons.php', 'debug_search.php', 'debug_search2.php', 'debug_path.php', 'dp2.php',
          'debug_search_v2.php', 'debug_search_v3.php', 'final_check.php', 'check_fix.php',
          's4.php', 's5.php', 's6.php', 's7.php', 'redownload_icons.php',
          'check_icon_status.php', 'fix_all.php'];
$root = '/www/wwwroot/web01.com/';
$admin = '/www/wwwroot/web01.com/admin/';
$removed = 0;
foreach ($files as $f) {
    foreach ([$root . $f, $admin . $f] as $path) {
        if (file_exists($path)) { unlink($path); $removed++; }
    }
}
header('Content-Type: text/plain');
echo "Removed $removed temp files\n";
