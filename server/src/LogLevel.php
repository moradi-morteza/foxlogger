<?php
namespace Foxlogger\Server;

enum LogLevel: string
{
    case Success = 'success';
    case Error   = 'error';
    case Info    = 'info';
    case Warning = 'warning';
    case Default = 'default';
}