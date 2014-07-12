<?php
/**
 * @author  chatwing
 * @package Chatwing_Api
 */

namespace Chatwing;

define('CHATWING_BASE_DIR', dirname(__FILE__));

class Api extends Object
{
    private $apiVersion = 1;

    // private information
    private $accessToken = null;
    private $clientId = null;

    /**
     * Indicate current environment
     *
     * @var string
     */
    private $environment = null;

    // environment constant
    const ENV_DEVELOPMENT = 'development';
    const ENV_PRODUCTION  = 'production';

    // agent constant
    const REQUEST_AGENT = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0';

    function __construct($accessToken, $clientId)
    {
        $this->accessToken = $accessToken;
        $this->clientId    = $clientId;

        $currentEnv = getenv('HTTP_CHATWING_ENV') ? getenv('HTTP_CHATWING_ENV') : self::ENV_PRODUCTION;
        $this->setEnv($currentEnv);
        $this->setAgent(
                "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0"
        ); // default user-agent
    }

    /**
     * Call the API action
     *
     * @param string $actionName
     * @param array  $params
     *
     * @return \Chatwing\Api\Response
     */
    public function call($actionName, $params = array())
    {
        // create action object. if action doesn't exist, 
        // then it throw an exception
        $action = new \Chatwing\Api\Action($actionName, $params);
        $curlHandler = $this->prepareConnection($action->getActionUri(), $action->getType());

        $result = curl_exec($curlHandler);
        $responseStatus = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);
        $result = json_decode($result, true);
        if(!$result){
            $result = array('message' => "Invalid response");
        }

        if($responseStatus != 200){
            throw new \Chatwing\Exception\ApiException($result, $responseStatus);
        }
        
        $response = new \Chatwing\Api\Response($result);
        return $response;
    }

    /**
     * Set application environment
     *
     * @param string $env
     */
    public function setEnv($env = \Chatwing\Api::ENV_PRODUCTION)
    {
        $this->environment = $env;
    }

    public function getEnv()
    {
        return $this->environment;
    }

    /**
     * Set API version
     *
     * @param int $version
     */
    public function setAPIVersion($version = 1)
    {
        $this->apiVersion = $version;
    }

    protected function prepareConnection($url, $requestType)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_VERBOSE, true);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_USERAGENT, $this->getAgent());
        curl_setopt($curlHandler, CURLOPT_URL, $url);

        return $curlHandler;
    }
}
