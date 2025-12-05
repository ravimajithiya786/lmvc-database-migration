<?php

namespace Regur\LMVC\Framework\Database\Libs;

class Dotenv
{
    protected string $path;

    private function __construct(string $path)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Create an immutable Dotenv instance
     */
    public static function createImmutable(string $path): self
    {
        return new self($path);
    }

    /**
     * Load .env into environment variables
     */
    public function load(): void
    {
        $file = $this->path . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($file)) {
            throw new \RuntimeException(".env file not found at: {$file}");
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Split into key=value
            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            // Set environment variables
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
