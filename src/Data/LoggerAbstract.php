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
}