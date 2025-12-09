<?php

class Log
{
    protected static string $fileName;
    protected static string $filePath;

    public static function create(string $fileName): void
    {
        self::$fileName = $fileName;
        $logDir = __DIR__ . '/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        self::$filePath = $logDir . '/' . self::$fileName . '.log';
    }

    public static function save($log): void
    {
        if (!isset(self::$filePath)) {
            throw new Exception("Log file is not created. Call Log::create() first.");
        }

        $timestamp = date('Y-m-d H:i:s');

        $log = preg_replace('/\e\[[0-9;]*m/', '', $log);

        if (is_array($log)) {
            $log = implode("\n", $log);
        }

        $entry = "[$timestamp] " . $log . "\n";

        file_put_contents(self::$filePath, $entry, FILE_APPEND);
    }
}
