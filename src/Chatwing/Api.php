<?php
/**
 * @author  chatwing
 * @package Chatwing_Api
 */

namespace Chatwing;

use Chatwing\Exception\ChatwingException;

define('CHATWING_BASE_DIR', dirname(__FILE__));

class Api extends Object
{
    private $apiVersion = 1;

    // private information
    private $accessToken = null;
    private $clientId = null;
    private $apiDomains = array(
        'development' => 'staging.chatwing.com',
        'production'  => 'chatwing.com'
    );


    /**
     * Indicate current environment
     *
     * @var string
     */
    private $environment = null;
    private $apiUrl = null;

    // environment constant
    const ENV_DEVELOPMENT = 'development';
    const ENV_PRODUCTION  = 'production';

    // agent constant
    const REQUEST_AGENT = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0';

    public function __construct($accessToken, $clientId, $apiVersion = 1)
    {
        $this->setAPIVersion($apiVersion);
        $this->accessToken = $accessToken;
        $this->clientId    = $clientId;

        $currentEnv = getenv('HTTP_CHATWING_ENV') ? getenv('HTTP_CHATWING_ENV') : self::ENV_PRODUCTION;
        $this->setEnv($currentEnv);
        $this->setAgent("Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0"); // default user-agent
    }

    /**
     * Call the API action
     *
     * @param string $actionName
     * @param array  $params
     *
     * @throws Exception\ChatwingException
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
            throw new \Chatwing\Exception\ChatwingException($result, $responseStatus);
        }
        
        $response = new \Chatwing\Api\Response($result);
        return $response;
    }

    /**
     * Set application environment
     *
     * @param string $env
     *
     * @throws ChatwingException
     */
    public function setEnv($env = \Chatwing\Api::ENV_PRODUCTION)
    {
        if(!in_array($env, array(self::ENV_PRODUCTION, self::ENV_DEVELOPMENT))){
            throw new ChatwingException(array('message' => "Enviroment is not supported"));
        }
        $this->environment = $env;
        $this->onEnvChange();
    }

    /**
     * Update settings after changing environment
     * @return void
     */
    protected function onEnvChange()
    {
        $this->apiUrl = $this->apiDomains[$this->getEnv()];
    }

    /**
     * Get environment
     * @return string
     */
    public function getEnv()
    {
        return $this->environment;
    }

    /**
     * Helper method to check if current enviroment is Development
     * @return boolean
     */
    public function isDevelopment()
    {
        return $this->getEnv() == self::ENV_DEVELOPMENT;
    }

    /**
     * Helper method to check if current enviroment is Production
     * @return boolean 
     */
    public function isProduction()
    {
        return $this->getEnv() == self::ENV_PRODUCTION;
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

    public function getAPIVersion()
    {
        return $this->apiVersion;
    }

    protected function prepareConnection($uri, $requestType)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_VERBOSE, true);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_USERAGENT, $this->getAgent());
        $queryUrl = $this->getQueryUrl($uri);

        curl_setopt($curlHandler, CURLOPT_URL, $queryUrl);

        return $curlHandler;
    }

    protected function getQueryUrl($uri = '')
    {
        return $this->apiUrl . '/api/' . $this->apiVersion . '/' . $uri;
    }
}
