<?php
$request_uri = '/XboxLive/sync/deals';
$script_name = '/XboxLive/index.php';

$basePath = str_replace('/index.php', '', $script_name);
$path = str_replace($basePath, '', $request_uri);
$path = $path === '' ? '/' : $path;

echo "Base Path: $basePath\n";
echo "Resolved Path: $path\n";
echo "Target Path: /sync/deals\n";
echo "Matches: " . ($path === '/sync/deals' ? 'YES' : 'NO') . "\n";
