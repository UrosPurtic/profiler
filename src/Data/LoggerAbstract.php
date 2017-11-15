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
     * @return $this
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

    public function getClientIp()
    {
        $tools = new \G4\Utility\Tools();
        $clientIp = $tools->getRealIP(false, ['HTTP_X_ND_CLIENT_IP']);
        return $clientIp ?: null;
    }
}