<?php

namespace G4\Profiler\Ticker;

class Formatter
{

    /**
     * @var \G4\Profiler\Ticker\Timer
     */
    private $timer;

    /**
     * @var string
     */
    private $uniqid;


    /**
     * @return array
     */
    public function getFormatted()
    {
        return [
            'elapsed_time' => $this->getFormattedTime($this->getTimer()->getElapsed()),
        ];
    }

    /**
     * @return \G4\Profiler\Ticker\Timer
     */
    public function getTimer()
    {
        return $this->timer;
    }

    /**
     * @return string
     */
    public function getUniqId()
    {
        if ($this->uniqid === null) {
            $this->uniqid = uniqid(null, true);
        }
        return $this->uniqid;
    }

    /**
     * @param \G4\Profiler\Ticker\Timer $timer
     * @return \G4\Profiler\Ticker\Formatter
     */
    public function setTimer(\G4\Profiler\Ticker\Timer $timer)
    {
        $this->timer = $timer;
        return $this;
    }

    /**
     * @param float $microtime
     * @return string
     */
    public function getFormattedTime($microtime)
    {
        return sprintf("%3f ms", $microtime * 1000);
    }
}