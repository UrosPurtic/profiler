<?php

namespace G4\Profiler\Ticker;

abstract class TickerAbstract implements TickerInterface
{

    /**
     * @var int
     */
    private $totalElapsedTime;

    /**
     * @var array
     */
    private $data;

    /**
     * @return int
     */
    public function getTotalElapsedTime()
    {
        return $this->totalElapsedTime;
    }

    /**
     * @return int
     */
    public function getTotalNumQueries()
    {
        return count($this->data);
    }

    /**
     * @param string $uniqueId
     * @return \G4\Profiler\Ticker\TickerAbstract
     */
    public function end($uniqueId)
    {
        $this->data[$uniqueId]->getTimer()->end();
        $this->totalElapsedTime += $this->data[$uniqueId]->getTimer()->getElapsed();
        return $this;
    }

    /**
     * @param string $uniqueId
     * @return \G4\Profiler\Ticker\Formatter
     */
    public function getDataPart($uniqueId)
    {
        return $this->data[$uniqueId];
    }

    /**
     * @return \G4\Profiler\Ticker\Formatter
     */
    public function getDataFormatterInstance()
    {
        return new Formatter();
    }

    /**
     * @return array
     */
    public function getFormatted()
    {
        return [
            'total_number_of_queries' => $this->getTotalNumQueries(),
            'total_elapsed_time'      => $this->getDataFormatterInstance()->getFormattedTime($this->getTotalElapsedTime()),
            'queries'                 => $this->getQueries(),
        ];
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        if (count($this->getData()) > 0) {
            foreach($this->getData() as $data) {
                $queries[] = $data->getFormatted();
            }
        }
        return isset($queries) ? $queries : [];
    }

    /**
     * @return string
     */
    public function start()
    {
        $formatter = $this->getDataFormatterInstance();
        $formatter
            ->setTimer((new \G4\Profiler\Ticker\Timer())->start());
        $this->data[$formatter->getUniqId()] = $formatter;
        return $formatter->getUniqId();
    }

    /**
     * @return array
     */
    private function getData()
    {
        return $this->getTotalNumQueries() > 0
            ? $this->data
            : [];
    }
}