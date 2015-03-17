<?php

namespace G4\Profiler\Ticker;

class Timer
{

    /**
     * @var float
     */
    private $ended;


    /**
     * @var float
     */
    private $started;


    /**
     * @return \G4\Profiler\Ticker\Timer
     */
    public function end()
    {
        $this->ended = microtime(true);
        return $this;
    }

    /**
     * @return float
     */
    public function getElapsed()
    {
        return $this->ended - $this->started;
    }

    /**
     * @return \G4\Profiler\Ticker\Timer
     */
    public function start()
    {
        $this->started = microtime(true);
        return $this;
    }
}