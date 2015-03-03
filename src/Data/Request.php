<?php

namespace G4\Profiler\Data;

class Request extends RequestResponseAbstarct
{

    /**
     * @var array
     */
    private $paramsToObfuscate;


    public function __construct()
    {
        $this->paramsToObfuscate = [];
    }

    /**
     * @param array $paramsToObfuscate
     * @return \G4\Profiler\Data\Request
     */
    public function setParamsToObfuscate(array $paramsToObfuscate)
    {
        $this->paramsToObfuscate = $paramsToObfuscate;
        return $this;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return [
            'id'        => $this->getId(),
            'timestamp' => time(),
            'datetime'  => date('Y-m-d H:i:s'),
            'ip'        => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli',
            'module'    => strtolower($this->getApplication()->getAppNamespace()),
            'service'   => strtolower($this->getApplication()->getRequest()->getResourceName()),
            'method'    => strtolower($this->getApplication()->getRequest()->getMethod()),
            'params'    => json_encode($this->obfuscateParams($this->getApplication()->getRequest()->getParams())),
        ];
    }

    /**
     *
     * @param array $params
     * @return array
     */
    private function obfuscateParams(array $params)
    {
        foreach($this->paramsToObfuscate as $key) {
            if (isset($params[$key])) {
                $params[$key] = '*****';
            }
        }
        return $params;
    }
}