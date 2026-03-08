<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * Get the appropriate channel based on request or manual service
     */
    public static function channel(?string $service = null)
    {
        if ($service) {
            return Log::channel($service);
        }

        // Try to get from request if available
        if (request()->has('log_channel')) {
            return Log::channel(request('log_channel'));
        }

        // Default to web channel
        return Log::channel('web');
    }

    /**
     * Log to the appropriate channel with all methods
     */
    public static function log($level, $message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->log($level, $message, $context);
    }

    public static function debug($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->debug($message, $context);
    }

    public static function info($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->info($message, $context);
    }

    public static function notice($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->notice($message, $context);
    }

    public static function warning($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->warning($message, $context);
    }

    public static function error($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->error($message, $context);
    }

    public static function critical($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->critical($message, $context);
    }

    public static function alert($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->alert($message, $context);
    }

    public static function emergency($message, array $context = [], ?string $service = null)
    {
        return self::channel($service)->emergency($message, $context);
    }
}
