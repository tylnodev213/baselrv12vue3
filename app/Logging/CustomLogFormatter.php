<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class CustomLogFormatter extends LineFormatter
{
    /**
     * Format a log record with custom context
     */
    public function format(LogRecord $record): string
    {
        // Get request context
        $context = $this->getRequestContext();
        
        // Add context to extra data
        $record->extra = array_merge($record->extra ?? [], $context);
        
        // Build custom log format
        $output = "[" . $record->datetime->format('Y-m-d H:i:s') . "] ";
        $output .= strtoupper($record->level->name) . ": ";
        
        // Add context attributes
        if (isset($context['host'])) {
            $output .= "[N]" . $context['host'] . " ";
        }
        if (isset($context['ip'])) {
            $output .= "[A]" . $context['ip'] . " ";
        }
        if (isset($context['script'])) {
            $output .= "[S]" . $context['script'] . " ";
        }
        if (isset($context['controller'])) {
            $output .= "[C]" . $context['controller'] . " ";
        }
        if (isset($context['action'])) {
            $output .= "[T]" . $context['action'] . " ";
        }
        if (isset($context['route'])) {
            $output .= "[R]" . $context['route'] . " ";
        }
        if (isset($context['user_agent'])) {
            $output .= "[U]" . $context['user_agent'] . " ";
        }
        if (isset($context['referer'])) {
            $output .= "[H]" . $context['referer'] . " ";
        }
        
        // Add main message
        $output .= $record->getMessage();
        
        // Add context data if any
        if (!empty($record->context)) {
            $output .= " " . json_encode($record->context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        $output .= "\n";
        
        return $output;
    }

    /**
     * Get request context from Laravel
     */
    private function getRequestContext(): array
    {
        $context = [];
        
        try {
            // Get HTTP request
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
                
                // Get controller action
                $action = $route->getActionName();
                if ($action && $action !== 'Closure') {
                    $context['controller'] = $action;
                    $context['action'] = $action;
                }
            }
            
            // User Agent
            $context['user_agent'] = $request->userAgent();
            
            // Referer
            $context['referer'] = $request->header('referer') ?? '';
            
        } catch (\Exception $e) {
            // If request context is not available, skip
        }
        
        return $context;
    }
}
