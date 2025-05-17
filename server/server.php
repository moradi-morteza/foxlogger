<?php

require_once __DIR__ . '/vendor/autoload.php';

use Foxlogger\Server\LogLevel;
use Foxlogger\Server\TerminalLogger;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Server as WebSocketServer;

class FoxLoggerServer
{
    private WebSocketServer $server;

    public function __construct(private readonly string $host,private readonly int $port)
    {
        $this->server = new WebSocketServer($this->host, $this->port);

        $this->server->set([
            'open_http2_protocol' => true,
            'enable_static_handler' => false,
        ]);

        $this->registerEvents();
    }

    public function start(): void
    {
        $this->server->start();
    }

    private function registerEvents(): void
    {
        $this->server->on('start', function (WebSocketServer $server) {
            $this->log("Server started on $this->host:$this->port", LogLevel::Success);
        });

        $this->server->on('workerStart', function ($server, $workerId) {
            $this->log("Worker #{$workerId} started");
        });

        $this->server->on('workerStop', function ($server, $workerId) {
            $this->log("Worker #{$workerId} stopped", LogLevel::Warning);
        });

        $this->server->on('request', function (Request $request, Response $response) {
            $this->log("HTTP request received: " . ($request->server['request_uri'] ?? '/'));
            $response->header('Content-Type', 'application/json');
            $response->end(json_encode([
                'message' => 'Hello from FoxLogger HTTP/2!',
                'path' => $request->server['request_uri'] ?? '/',
            ]));
        });

        $this->server->on('open', function (WebSocketServer $server, $request) {
            $this->log("WebSocket connection opened: #{$request->fd}", LogLevel::Success);
        });

        $this->server->on('message', function (WebSocketServer $server, $frame) {
            $this->log("Received message from #{$frame->fd}: {$frame->data}");
            $server->push($frame->fd, json_encode([
                'echo' => $frame->data,
                'time' => time(),
            ]));
        });

        $this->server->on('close', function (WebSocketServer $server, $fd) {
            $this->log("Connection closed: #{$fd}", LogLevel::Warning);
        });

        $this->server->on('shutdown', function ($server) {
            $this->log("Server shutting down...", LogLevel::Error);
        });
    }

    private function log(string $title, LogLevel $level = LogLevel::Info): void
    {
        TerminalLogger::log($title, $level);
    }
}

$logger = new FoxLoggerServer('0.0.0.0',7000);
$logger->start();
