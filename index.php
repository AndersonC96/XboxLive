<?php
require_once __DIR__ . '/vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;

if (AuthService::check()) {
    header('Location: pages/dashboard.php');
} else {
    header('Location: pages/login.php');
}
exit();