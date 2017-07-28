<?php

namespace G4\Profiler\Data;

abstract class LoggerAbstract extends \G4\DataMapper\Domain\DomainAbstract
{

    /**
     * @var float
     */
    private $startTime;

    /**
     * @return float
     */
    public function getElapsedTime()
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * @param float $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * Return javascript timestamp with milliseconds
     * @return int
     */
    public function getJsTimestamp()
    {
        return (int) (microtime(true) * 1000);
    }
}