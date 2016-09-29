<?php

namespace G4\Profiler\Data;

class RuntimeLog extends LoggerAbstract
{
    /**
     * @var mixed
     */
    private $loggedData;

    /**
     * @var string
     */
    private $tag;

    /**
     * @param mixed $var
     * @param string $tag
     */
    public function __construct($var, $tag)
    {
        $this->loggedData = $var;
        $this->tag        = $tag;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        $index = 2;
        $trace = debug_backtrace();
        $line = isset($trace[$index]['line']) ? $trace[$index]['line'] : null;
        $file = isset($trace[$index]['file']) ? $trace[$index]['file'] : null;

        return [
            'id'        => $this->getId(),
            'timestamp' => time(),
            'datetime'  => date('Y-m-d H:i:s'),
            'ip'        => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli',
            'file'      => $file,
            'line'      => $line,
            'data'      => var_export($this->loggedData, true),
            'tag'       => $this->tag ? $this->tag : '',
        ];
    }

}