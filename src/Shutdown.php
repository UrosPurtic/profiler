<?php

namespace G4\Profiler;

class Shutdown extends ErrorAbstract
{

    private $error;


    public function handle()
    {
        $this->error = error_get_last();
        if ($this->hasError()) {
            $this->getErrorData()
                ->setCode($this->error['type'])
                ->setMessage('[SHUTDOWN] ' . $this->error['message'])
                ->setFile($this->filterFilePath($this->error['file']))
                ->setLine($this->error['line']);
            $this
                ->display()
                ->log();
        }
    }

    private function hasError()
    {
        return $this->error !== null;
    }
}