<?php

namespace G4\Profiler;

class ErrorData
{

    private $code;

    private $file;

    private $exceptionFlag;

    private $line;

    private $message;

    private $trace;


    public function getContext()
    {
        return [
            'REQUEST' => $_REQUEST,
            'SERVER'  => $_SERVER,
        ];
    }

    public function getDataAsString()
    {
        return join(PHP_EOL, [
            strtoupper($this->getName()) . ": {$this->getMessage()}",
            "LINE: {$this->getLine()}",
            "FILE: {$this->getFile()}",
        ]) . PHP_EOL;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getName()
    {
        return $this->exceptionFlag === true
            ? 'exception'
            : ErrorCodes::getName($this->getCode());
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTrace()
    {
        return empty($this->trace)
            ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            : $this->trace;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setTrace($trace)
    {
        $this->trace = $trace;
        return $this;
    }

    public function thisIsException()
    {
        $this->exceptionFlag = true;
        return $this;
    }
}