<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

    session_unset();
    session_destroy();
    header("Location: ../pages/login.php");
    exit();