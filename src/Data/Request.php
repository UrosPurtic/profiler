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
            'timestamp' => $this->getJsTimestamp(),
            'datetime'  => date('Y-m-d H:i:s'),
            'ip'        => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli',
            'module'    => strtolower($this->getApplication()->getAppNamespace()),
            'service'   => strtolower($this->getApplication()->getRequest()->getResourceName()),
            'method'    => strtolower($this->getApplication()->getRequest()->getMethod()),
            'params'    => json_encode($this->obfuscateParams($this->getApplication()->getRequest()->getParams())),
            'hostname'  => gethostname(),
            'app_key'   => $this->getApplication()->getRequest()->getParam('X-ND-AppKey') ?: null,
            'app_token' => $this->getApplication()->getRequest()->getParam('X-ND-AppToken') ?: null,
            'authentication' => $this->getApplication()->getRequest()->getParam('X-ND-Authentication') ?: null,
            'client_ip' => $this->getClientIp(),
            'app_name'  => $this->getAppName(),
        ];
    }

    /**
     *
     * @param array $params
     * @return array
     */
    private function obfuscateParams(array $params)
    {
        $rawBodyParams = parse_query(trim(file_get_contents('php://input')));

        foreach($this->paramsToObfuscate as $key) {
            if (isset($params[$key])) {
                $params[$key] = '*****';
            }
            if (is_array($rawBodyParams) && array_key_exists($key, $rawBodyParams)) {
                $rawBodyParams[$key] = '_obfuscated_';
            }
        }

        $params['raw_body'] = http_build_query($rawBodyParams);
        return $params;
    }
}