<?php

require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

// Backward compatibility: expose $pdo variable
$pdo = \Anderson\XboxLive\Core\Database::getInstance();