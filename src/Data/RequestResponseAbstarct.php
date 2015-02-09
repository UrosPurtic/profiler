<?php

namespace G4\Profiler\Data;

abstract class RequestResponseAbstarct extends LoggerAbstract
{
    /**
     * @var \G4\CleanCore\Application
     */
    private $application;

    /**
     * @return \G4\CleanCore\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param \G4\CleanCore\Application $application
     * @return \G4\Profiler\Data\LoggerAbstract
     */
    public function setApplication(\G4\CleanCore\Application $application)
    {
        $this->application = $application;
        return $this;
    }
}