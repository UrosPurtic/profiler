<?php

namespace G4\Profiler\Data;

class TaskerEnd extends LoggerAbstract
{

    private $type;

    public function getRawData()
    {
        return [
            'id'           => $this->getId(),
            'type'         => $this->type,
            'elapsed_time' => $this->getElapsedTime(),
        ];
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}