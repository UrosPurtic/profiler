<?php

namespace G4\Profiler\Data;

class TaskerStart extends LoggerAbstract
{

    private $options;

    public function getRawData()
    {
        return [
            'id'        => $this->getId(),
            'timestamp' => time(),
            'datetime'  => date('Y-m-d H:i:s'),
            'options'   => json_encode($this->options),
            'hostname'  => gethostname(),
            'pid'       => getmypid()
        ];
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}