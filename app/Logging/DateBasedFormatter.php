<?php

namespace App\Logging;

use Monolog\Logger;

class DateBasedFormatter
{
    /**
     * Configure the logger to use date-based folder structure
     */
    public function __invoke(Logger $logger): void
    {
        $handler = new DateBasedStreamHandler(
            basePath: storage_path('logs'),
            level: config('logging.level', Logger::DEBUG)
        );
        
        $logger->pushHandler($handler);
    }
}
