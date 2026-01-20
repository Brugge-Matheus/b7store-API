<?php

namespace App;

use Psr\Log\AbstractLogger;

class SwaggerLogger extends AbstractLogger
{
    public function log($level, $message, array $context = []): void
    {
        // Ignora warnings sobre PathItem
        if (strpos($message, 'PathItem') !== false) {
            return;
        }
        
        // Log outros erros normalmente
        if ($level === 'error') {
            error_log("Swagger {$level}: {$message}");
        }
    }
}
