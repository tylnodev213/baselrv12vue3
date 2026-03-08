<?php

namespace App\Logging;

use Monolog\LogRecord;

class RequestContextProcessor
{
    /**
     * Add request context to log record
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $this->getRequestContext();
        
        // Add context to the record
        $record->extra = array_merge($record->extra ?? [], $context);
        
        // Build formatted message with context
        $message = $record->message;
        
        // Add context attributes to message
        $contextStr = '';
        if (isset($context['host'])) {
            $contextStr .= " [N]" . $context['host'];
        }
        if (isset($context['ip'])) {
            $contextStr .= " [A]" . $context['ip'];
        }
        if (isset($context['script'])) {
            $contextStr .= " [S]" . $context['script'];
        }
        if (isset($context['controller'])) {
            $contextStr .= " [C]" . $context['controller'];
        }
        if (isset($context['action'])) {
            $contextStr .= " [T]" . $context['action'];
        }
        if (isset($context['route'])) {
            $contextStr .= " [R]" . $context['route'];
        }
        if (isset($context['user_agent'])) {
            $contextStr .= " [U]" . $context['user_agent'];
        }
        if (isset($context['referer'])) {
            $contextStr .= " [H]" . $context['referer'];
        }
        
        // Update message with context
        if ($contextStr) {
            $record->message = $message . $contextStr;
        }
        
        return $record;
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
