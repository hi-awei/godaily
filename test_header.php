<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<h1>Testing header.php</h1>";
require_once 'templates/header.php';
echo "<p>header loaded OK</p>";
echo "<p>currentPage=$currentPage</p>";
echo "<p>SITE_NAME=" . SITE_NAME . "</p>";
require_once 'templates/footer.php';
echo "<p>ALL OK</p>";
