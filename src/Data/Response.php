<?php

namespace G4\Profiler\Data;

class Response extends RequestResponseAbstarct
{

    /**
     * @return array
     */
    public function getRawData()
    {
        $resource   = $this->getApplication()->getResponse()->getResponseObject();
        $appMessage = $this->getApplication()->getResponse()->getResponseMessage();
        return [
            'id'           => $this->getId(),
            'code'         => $this->getApplication()->getResponse()->getHttpResponseCode(),
            'message'      => $this->getApplication()->getResponse()->getHttpMessage(),
            'resource'     => empty($resource) ? null : json_encode($resource),
            'app_code'     => $this->getApplication()->getResponse()->getApplicationResponseCode(),
            'app_message'  => empty($appMessage) ? null : json_encode($appMessage),
            'elapsed_time' => $this->getElapsedTime(),
        ];
    }
}