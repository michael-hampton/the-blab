<?php

error_reporting (E_ALL);
ini_set ("display_errors", 1);

use Phalcon\Mvc\Application;
use Phalcon\Config\Adapter\Ini as ConfigIni;

//$_GET['_url'] = '/contact/send';
//$_SERVER['REQUEST_METHOD'] = 'POST';
try {
    define ('APP_PATH', realpath ('..') . '/');
    /**
     * Read the configuration
     */
    $config = new ConfigIni (APP_PATH . 'app/config/config.ini');
    /**
     * Auto-loader configuration
     */
    require APP_PATH . 'app/config/loader.php';
    
    /**
     * Load application services
     */
    require APP_PATH . 'app/config/services.php';
    $application = new Application ($di);
    define("EMAIL_ADDRESS", "mike@blab.x10host.com");

    

    function process_error_backtrace ($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ( !(error_reporting () & $errno) )
        {
            return;
        }
        
        switch ($errno) {
            case E_WARNING :
            case E_USER_WARNING :
            case E_STRICT :
            case E_NOTICE :
            case E_USER_NOTICE :
                $type = 'warning';
                $fatal = false;
                break;
            default :
                $type = 'fatal error';
                $fatal = true;
                break;
        }
        $trace = array_reverse (debug_backtrace ());
        array_pop ($trace);

        $message = "";

        if ( php_sapi_name () == 'cli' )
        {
            $message .= 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
            foreach ($trace as $item) {
                $message .= '  ' . (isset ($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset ($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()' . "\n";
            }
        }
        else
        {
            $message .= '<p class="error_backtrace">' . "\n";
            $message .= '  Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
            $message .= '  <ol>' . "\n";
            foreach ($trace as $item) {
                $message .= '    <li>' . (isset ($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset ($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()</li>' . "\n";
            }
            $message .= '  </ol>' . "\n";
            $message .= '</p>' . "\n";
        }
        if ( ini_get ('log_errors') )
        {
            $items = array();
            foreach ($trace as $item) {
                $items[] = (isset ($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset ($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()';
            }
            $message = 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ': ' . join (' | ', $items);
            error_log ($message);
        }

       $objLog = new EmailLog(array(EMAIL_ADDRESS));
       $objLog->logError($message, $errno);

        if ( $fatal )
        {
            exit (1);
        }
    }

    set_error_handler ('process_error_backtrace');

    echo $application->handle ()->getContent ();
} catch (Exception $e) {
    echo $e->getMessage ();
}