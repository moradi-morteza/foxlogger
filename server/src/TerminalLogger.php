<?php

namespace Foxlogger\Server;

class TerminalLogger
{
    private const COLOR_RESET = "\033[0m";

    private const COLORS = [
        LogLevel::Success->name => "\033[32m", // green
        LogLevel::Error->name => "\033[31m", // red
        LogLevel::Info->name => "\033[36m", // cyan
        LogLevel::Warning->name => "\033[33m", // yellow
        LogLevel::Default->name => "\033[37m", // white
    ];

    public static function log(string $message, LogLevel $level = LogLevel::Default): void
    {
        $color = self::COLORS[$level->name] ?? self::COLORS[LogLevel::Default->name];
        echo $color . $message . self::COLOR_RESET . PHP_EOL;
    }

    public static function success(string $message): void
    {
        self::log($message, LogLevel::Success);
    }

    public static function error(string $message): void
    {
        self::log($message, LogLevel::Error);
    }

    public static function info(string $message): void
    {
        self::log($message, LogLevel::Info);
    }

    public static function warning(string $message): void
    {
        self::log($message, LogLevel::Warning);
    }
}
