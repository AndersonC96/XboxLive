<?php

namespace Anderson\XboxLive\Core;

class Cache
{
    private static string $cacheDir = __DIR__ . '/../../storage/cache';

    public static function set(string $key, $data, int $expiration = 86400): void
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }

        $filename = self::$cacheDir . '/' . md5($key) . '.cache';
        $cacheData = [
            'expires' => time() + $expiration,
            'data' => $data
        ];

        file_put_contents($filename, serialize($cacheData));
    }

    public static function get(string $key)
    {
        $filename = self::$cacheDir . '/' . md5($key) . '.cache';

        if (!file_exists($filename)) {
            return null;
        }

        $cacheData = unserialize(file_get_contents($filename));

        if (time() > $cacheData['expires']) {
            unlink($filename);
            return null;
        }

        return $cacheData['data'];
    }
}
