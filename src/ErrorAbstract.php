<?php

namespace G4\Profiler;

abstract class ErrorAbstract
{

    /**
     * @var ErrorData
     */
    private $errorData;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var \G4\Log\Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $pathRoot;


    public function __construct()
    {
    }

    public function getBackTrace()
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    public function getErrorData()
    {
        if (!$this->errorData instanceof ErrorData) {
            $this->errorData = new ErrorData();
        }
        return $this->errorData;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    public function setLogger(\G4\Log\Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setPathRoot($pathRoot)
    {
        $this->pathRoot = $pathRoot;
        return $this;
    }

    public function display()
    {
        if ($this->shouldDisplay()) {
            $presenter = new Presenter();
            $presenter
                ->setData($this->errorData)
                ->display();
        }
        return $this;
    }

    public function filterFilePath($file)
    {
        return $this->pathRoot === null
            ? $file
            : str_replace(realpath($this->pathRoot), '', $file);
    }

    public function log()
    {
        if ($this->logger instanceof \G4\Log\Logger) {
            $loggerData = new \G4\Profiler\Data\Error();
            $loggerData
                ->setErrorData($this->errorData);
            $this->logger->log($loggerData);
        }
        return $this;
    }

    private function shouldDisplay()
    {
        return $this->debug
            && error_reporting()
            && $this->errorData->getCode();
    }
}