<?php
/**
 * Debug Class
 *
 * Handle all debug actions
 *
 * @package    G4
 * @author     Dejan Samardzija, samardzija.dejan@gmail.com
 * @copyright  Little Genius Studio www.littlegeniusstudio.com All rights reserved
 * @version    1.0
 */

namespace G4\Profiler;
use G4\Buffer\Buffer;
use G4\Log\Writer;

class Debug
{
    /**
     * Errors/Exceptions styling
     * @var string
     */
    const CSS = 'position: relative;background: #ffdfdf;border: 2px solid #ff5050;margin: 5px auto;padding: 10px;
        min-width: 720px;width: 50%;font: normal 12px "Verdana", monospaced;overflow: auto;z-index: 100;clear: both;';

    /**
     * Default Redis buffer connection params
     * @var array
     */
    private static $bufferConn = array();

    /**
     * IP addresses that are allowed to print exception stack trace
     * @var array
     */
    private static $_allowDebugIp = array();

    /**
     * URL param phrase to trigger quiet mode (do now render exceptions even for allowed IP's)
     * @var array
     */
    private static $_debugTriggerQuiet = 'no-debug';

    /**
     * URL param phrase to trigger allowed hosts refreshing
     * @var string
     */
    private static $_debugTriggerRefresh = 'debug-refresh-allowed-hosts';

    /**
     * Log file extension
     * @var string
     */
    private static $_extenstion = '.log';

    /**
     * Default buffer size
     * @var int
     */
    private static $_defaultSize = 100;

    /**
     * Separator for json parser
     * @var string
     */
    private static $_separator = "##########\n";

    /**
     * Flag is ajax or cli request
     * @var bool
     */
    private static $_isAjaxOrCli;

    /**
     * Default error handler
     *
     * @param  int    $errno   - error number
     * @param  string $errstr  - error string
     * @param  string $errfile - filename where error occured
     * @param  int    $errline - line number in the filename where error occured
     * @return void
     */
    public static function handlerError($errno, $errstr, $errfile, $errline)
    {
        $bad = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

        // set log file name
        switch ($errno) {
            case E_ERROR:             $fn = "error";                 break;
            case E_WARNING:           $fn = "warning";               break;
            case E_PARSE:             $fn = "parse";                 break;
            case E_NOTICE:            $fn = "notice";                break;
            case E_CORE_ERROR:        $fn = "core_error";            break;
            case E_CORE_WARNING:      $fn = "core_warning";          break;
            case E_COMPILE_ERROR:     $fn = "compile_error";         break;
            case E_COMPILE_WARNING:   $fn = "compile_warning";       break;
            case E_USER_ERROR:        $fn = "user_error";            break;
            case E_USER_WARNING:      $fn = "user_warning";          break;
            case E_USER_NOTICE:       $fn = "user_notice";           break;
            case E_STRICT:            $fn = "strict";                break;
            case E_RECOVERABLE_ERROR: $fn = "recoverable_error";     break;
            case E_DEPRECATED:        $fn = "deprecated";            break;
            case E_USER_DEPRECATED:   $fn = "user_deprecated";       break;
            case E_ALL:               $fn = "all";                   break;
            default:                  $fn = "__{$errno}__undefined"; break;
        }

        if(defined('PATH_ROOT')) {
            $errfile = str_replace(realpath(PATH_ROOT), '', $errfile);
        }

        $err_msg = strtoupper($fn) . ": {$errstr} \n line: {$errline} \n file: {$errfile}";

        // append request data
        $err_msg .= self::formatRequestData();

        // With this setup errors are displayed according to the error_reporting() setting
        // but all errors are logged to file, regardles of
        if(defined('DEBUG') && DEBUG && error_reporting() & $errno) {
            echo self::_skipFormating()
                ? $err_msg . PHP_EOL
                : sprintf("<pre><p style='%s'>%s</p></pre>", self::CSS, $err_msg);
        }

        // all errors are logged
        if(!empty($fn)) {
            self::_writeLog($fn . self::$_extenstion, self::formatHeaderWithTime() . $err_msg);

            // write json logs also, so we have it prepared for solr integration
            $full_error = array(
                'type'     => $fn,
                'message'  => $errstr,
                'code'     => $errno,
                'line'     => $errline,
                'file'     => $errfile,
                'datetime' => date('Y-m-d H:i:s'),
                'tz'       => date_default_timezone_get(),
                'context'  => self::formatRequestData(false),
            );

            $json_msg = phpversion() < '5.4'
                ? self::$_separator . json_encode($full_error)
                : self::$_separator . json_encode($full_error, JSON_PRETTY_PRINT);
            self::_writeLog('____json__' . $fn . self::$_extenstion, $json_msg);
            // end json
        }

        // On production setup, if bad errors occurs, send mail with parsed exception and terminate the script
        // DISABLED FOR NOW SINCE IT'S KILLING US AT THE MOMENT!!!!!
//         if(in_array($errno, $bad)) {
//             if(defined('DEBUG') && !DEBUG) {
//                 try {
//                     throw new \Exception($errstr);
//                 } catch (\Exception $e) {
//                     $msg = self::_parseException($e);
//                 }

//                 defined('EMAIL_DEBUG')
//                 && defined('EMAIL_DEBUG_FROM')
//                 && @mail (EMAIL_DEBUG, "PHP ERROR: {$errstr}", $msg, 'From: ' . EMAIL_DEBUG_FROM);
//             }
//             exit(1);
//         }
    }

    /**
     * Handle exceptions
     *
     * @param  Exception $e
     * @return void
     */
    public static function handlerException(\Exception $e, $print = true)
    {
        $file = 'exception' . self::$_extenstion;

        // parse exception data into string
        $msg = strip_tags(self::_parseException($e));

        $out = self::_skipFormating()
            ? "Exception: {$msg}"
            : sprintf("<div style='%s'><strong>Exception:</strong><br />%s</div>", self::CSS, nl2br($msg));

        self::_writeLog($file, self::formatHeaderWithTime() . $msg);

        // write json logs also, so we have it prepared for solr integration
        $full_error = array(
            'type'     => get_class($e),
            'message'  => $e->getMessage(),
            'code'     => $e->getCode(),
            'line'     => $e->getLine(),
            'file'     => $e->getFile(),
            'trace'    => $e->getTrace(),
            'datetime' => date('Y-m-d H:i:s'),
            'tz'       => date_default_timezone_get(),
            'context'  => self::formatRequestData(false),
        );

        $json_msg = phpversion() < '5.4'
            ? self::$_separator . json_encode($full_error)
            : self::$_separator . json_encode($full_error, JSON_PRETTY_PRINT);

        self::_writeLog('____json__' . $file, $json_msg);
        // end json

        // override print for allowed IP
        $print = isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], self::$_allowDebugIp) && !isset($_GET[self::$_debugTriggerQuiet])
            ? true
            : $print;

        return $print ? print($out) : true;
    }

    /**
     * Error handler for fatal errors
     * @return void
     */
    public static function handlerShutdown()
    {
        $error = error_get_last();
        if($error !== NULL) {
            self::handlerError($error['type'], '[SHUTDOWN] ' . $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Write log with buffer support
     * @param  string $filename
     * @param  string $msg
     * @return void
     */
    private static function _writeLog($filename, $msg)
    {
        if(is_array(self::$bufferConn) && !empty(self::$bufferConn)) {

            $callback = function($data) use ($filename) {
                foreach($data as $item) {
                    Writer::writeLogVerbose($filename, $item);
                }
            };

            $size = isset(self::$bufferConn['size'])
                    && is_int(self::$bufferConn['size'])
                    && self::$bufferConn['size'] > 0
                    && self::$bufferConn['size'] < self::$_defaultSize
                ? self::$bufferConn['size']
                : self::$_defaultSize;

            $buffer = new Buffer($filename, Buffer::ADAPTER_REDIS, $size, $callback, self::$bufferConn);
            $buffer->add($msg);

        } else {
            Writer::writeLogVerbose($filename, $msg);
        }
    }

    /**
     * Parses exceptions into human readable format + html styling
     *
     * @param  Exception $e          Exception object that's being parsed
     * @param  bool      $plain_text flag to return formated exception, or plain text
     *
     * @todo: finish plain text implemntation
     *
     * @return string
     */
    private static function _parseException(\Exception $e, $plain_text = false)
    {
        $exc_msg   = $e->getMessage();
        $exc_code  = $e->getCode();
        $exc_line  = $e->getLine();
        $exc_file  = basename($e->getFile());
        $exc_trace = $e->getTrace();

        $s = "<em style='font-size:larger;'>{$exc_msg}</em> (# {$exc_code})<br /> LINE: <b>#{$exc_line}</b> FILE: <u>{$exc_file}</u>\n\n";

        foreach ($exc_trace as $key => $row) {
            $s .= '<span class="traceLine">#' . ($key++) . ' ';

            if (!empty($row['function'])) {
                $s .= "<b>";
                if (!empty($row['class'])) {
                    $s .= $row['class'] . $row['type'];
                }

                $s .= "{$row['function']}</b>()";
            }

            if (!empty($row['file'])) {
                $s .= " LINE: <b>#{$row['line']}</b> FILE: <u>" . basename($row['file']) . '</u>';
            }

            $s .= '</span>' . PHP_EOL;
        }

        $s .= self::formatRequestData();

        return $s;
    }

    /**
     * Returns all request data formated into string
     * @return string
     */
    public static function formatRequestData($asString = true)
    {
        $fromServer = array(
            'REQUEST_URI',
            'REQUEST_METHOD',
            'HTTP_REFERER',
            'QUERY_STRING',
            'HTTP_USER_AGENT',
            'REMOTE_ADDR',
        );

        if($asString) {
            $s = "\n\n";
            $s .= isset($_REQUEST)? "REQUEST: " . print_r($_REQUEST, true) . PHP_EOL   : '';
            foreach ($fromServer as $item) {
                $s .= isset($_SERVER[$item]) ? "{$item}: {$_SERVER[$item]}\n" : '';
            }
        } else {
            $s = array(
                'REQUEST' => $_REQUEST,
                'SERVER'  => $_SERVER,
            );
        }

        return $s;
    }

    /**
     * Returns full date and time with separating characters
     * @return string
     */
    public static function formatHeaderWithTime()
    {
        $date = date("Y-m-d H:i:s");
        return PHP_EOL . str_repeat('-', 10) . " {$date} " . str_repeat('-', 100) . PHP_EOL;
    }

    /**
     * Add IP address that is allowed to print exception stack
     * @param string $ip IP address
     * @return int number of added values
     *
     * @todo: check if IP is valid
     * @todo: allow adding of array
     */
    public static function addAllowedIp($ip)
    {
        return array_push(self::$_allowDebugIp, (string)$ip);
    }

    /**
     * Return array of allowed IP's to print exception stack
     * @return array
     *
     * @todo: parsing allowed hosts
     * @todo: caching
     * @todo: refreshing trigger
     */
    public static function getAllowedIp($ip)
    {
        return self::$_allowDebugIp;
    }

    /**
     * Add URL host that is allowed to print exception stack
     * @param string $ip
     * @return number number of added values
     *
     * @todo: not finished
     */
    public static function addAllowedHost($ip)
    {
        return false;
    }

    /**
     * Check if request is ajax or cli and skip formating
     *
     * @return bool
     */
    private static function _skipFormating()
    {
        if(null !== self::$_isAjaxOrCli) {
            return self::$_isAjaxOrCli;
        }

        self::$_isAjaxOrCli =
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            || (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']));

        return self::$_isAjaxOrCli;
    }
}