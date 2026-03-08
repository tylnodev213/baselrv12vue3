<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class ContextFormatter extends LineFormatter
{
    private const DEFAULT_FORMAT = "[%datetime%] %channel%.%level_name%: %message%\n";

    public function __construct($format = null, $dateFormat = null, bool $allowInlineLineBreaks = false, bool $includeStacktraces = false)
    {
        parent::__construct($format ?? self::DEFAULT_FORMAT, $dateFormat, $allowInlineLineBreaks, $includeStacktraces);
    }

    public function format(LogRecord $record): string
    {
        // Get request context
        $context = $this->getRequestContext();
        
        // Build context string
        $contextStr = '';
        if (!empty($context['host'])) {
            $contextStr .= " [N]" . $context['host'];
        }
        if (!empty($context['ip'])) {
            $contextStr .= " [A]" . $context['ip'];
        }
        if (!empty($context['script'])) {
            $contextStr .= " [S]" . $context['script'];
        }
        if (!empty($context['controller'])) {
            $contextStr .= " [C]" . $context['controller'];
        }
        if (!empty($context['action'])) {
            $contextStr .= " [T]" . $context['action'];
        }
        if (!empty($context['route'])) {
            $contextStr .= " [R]" . $context['route'];
        }
        if (!empty($context['user_agent'])) {
            $contextStr .= " [U]" . substr($context['user_agent'], 0, 100);
        }
        if (!empty($context['referer'])) {
            $contextStr .= " [H]" . $context['referer'];
        }
        
        // Add context to message
        if ($contextStr) {
            $record->message .= $contextStr;
        }
        
        // Call parent format
        return parent::format($record);
    }

    /**
     * Get request context from Laravel
     */
    private function getRequestContext(): array
    {
        $context = [];
        
        try {
            // Check if we're in HTTP context
            if (!function_exists('request')) {
                return $context;
            }

            $request = request();
            
            // Host/Domain
            $context['host'] = $request->getHost();
            
            // IP Address
            $context['ip'] = $request->ip();
            
            // Script name
            $context['script'] = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            
            // Current route and action
            $route = $request->route();
            if ($route) {
                $context['route'] = $request->getPathInfo();
                
                // Get controller and action
                $action = $route->getActionName();
                if ($action && $action !== 'Closure') {
                    $context['controller'] = $action;
                    $context['action'] = $action;
                }
            }
            
            // User Agent
            $context['user_agent'] = $request->userAgent() ?? '';
            
            // Referer
            $context['referer'] = $request->header('Referer') ?? '';
            
        } catch (\Exception $e) {
            // Silently ignore errors in context collection
        }
        
        return $context;
    }
}
