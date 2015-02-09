<?php

namespace G4\Profiler;

class Exception extends ErrorAbstract
{


    public function handle(\Exception $exception)
    {
        $this->getErrorData()
            ->setCode($exception->getCode())
            ->setMessage($exception->getMessage())
            ->setFile($this->filterFilePath($exception->getFile()))
            ->setLine($exception->getLine())
            ->setTrace($exception->getTrace())
            ->thisIsException();
        $this
            ->display()
            ->log();
    }
}