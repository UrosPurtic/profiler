<?php

namespace G4\Profiler\Data;


class Security extends RequestResponseAbstarct
{
    /**
     * @var \G4\Runner\Profiler
     */
    private $profiler;

    /**
     * @return array
     */
    public function getRawData()
    {
        $resource   = $this->getApplication()->getResponse()->getResponseObject();
        $appMessage = $this->getApplication()->getResponse()->getResponseMessage();

        return [
            'id'          => $this->getId(),
            'code'        => $this->getApplication()->getResponse()->getHttpResponseCode(),
            'message'     => $this->getApplication()->getResponse()->getHttpMessage(),
            'resource'    => empty($resource) ? null : json_encode($resource),
            'timestamp'   => $this->getJsTimestamp(),
            'datetime'    => date('Y-m-d H:i:s'),
            'ip'          => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli',
            'module'      => strtolower($this->getApplication()->getAppNamespace()),
            'service'     => strtolower($this->getApplication()->getRequest()->getResourceName()),
            'method'      => strtolower($this->getApplication()->getRequest()->getMethod()),
            'params'      => json_encode($this->getApplication()->getRequest()->getParams()),
            'app_code'    => $this->getApplication()->getResponse()->getApplicationResponseCode(),
            'app_message' => empty($appMessage) ? null : json_encode($appMessage),
            'hostname'    => gethostname(),
            'profiler'    => json_encode($this->profiler->getProfilerOutput()),
            'app_key'     => $this->getApplication()->getRequest()->getParam('X-ND-AppKey'),
            'app_token'   => $this->getApplication()->getRequest()->getParam('X-ND-AppToken'),
            'authentication' => $this->getApplication()->getRequest()->getParam('X-ND-Authentication'),
        ];
    }

    public function setProfiler(\G4\Runner\Profiler $profiler)
    {
        $this->profiler = $profiler;
        return $this;
    }
}