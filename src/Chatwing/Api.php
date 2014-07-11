<?php
/**
 * @author  chatwing
 * @package Chatwing_Api
 */

namespace Chatwing;

class Api extends Object
{
    // private information
    private $_access_token = null;
    private $_client_id = null;

    private $_enviroment = null;

    // environment constant
    const ENV_DEVELOPMENT = 'development';
    const ENV_PRODUCTION  = 'production';

    function __construct($accessToken, $clientId)
    {
        $this->_access_token = $accessToken;
        $this->_client_id    = $clientId;

        $currentEnv = getenv('HTTP_CHATWING_ENV') ? getenv('HTTP_CHATWING_ENV') : self::ENV_PRODUCTION;
        $this->setEnv($currentEnv);
    }

    /**
     * Call the API action
     * @param       $action
     * @param array $params
     */
    public function call($action, $params = array())
    {

    }

    /**
     * 
     * @param string $env
     */
    public function setEnv($env = \Chatwing\Api::ENV_PRODUCTION)
    {
        $this->_enviroment = $env;
    }

    public function getEnv()
    {
        return $this->_enviroment;
    }    
}
