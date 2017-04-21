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
            ->setTrace($this->getTrace($exception))
            ->thisIsException();
        $this
            ->display()
            ->log();
    }

    private function getTrace(\Exception $exception)
    {
        $e = $exception;
        while ($e->getPrevious() !== null) {
            $e = $e->getPrevious();
        }
        return $e->getTrace();
    }
}