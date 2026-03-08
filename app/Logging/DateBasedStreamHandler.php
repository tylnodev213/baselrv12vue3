<?php

namespace App\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Formatter\FormatterInterface;

class DateBasedStreamHandler extends StreamHandler
{
    protected string $basePath;
    protected string $service = '';

    public function __construct(string $basePath = null, string $service = '', $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->basePath = $basePath ?? storage_path('logs');
        $this->service = $service;
        
        // Initialize with a placeholder path, will be overridden in write()
        parent::__construct($this->basePath . '/placeholder.log', $level, $bubble);
        
        // Set custom formatter
        $this->setFormatter(new ContextFormatter());
    }

    public function write(LogRecord $record): void
    {
        // Create date-based directory structure
        $date = $record->datetime->format('Y-m-d');
        $level = strtolower($record->level->name);
        
        // Create the full path: storage/logs/[service]/2026-03-09/error.log
        $servicePath = $this->service ? $this->service . '/' : '';
        $logPath = $this->basePath . '/' . $servicePath . $date . '/' . $level . '.log';
        
        // Ensure directory exists
        $directory = dirname($logPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Update the stream URL for this write
        $this->url = $logPath;
        $this->stream = null; // Reset stream so it opens the new file
        
        // Call parent write method
        parent::write($record);
    }
}
