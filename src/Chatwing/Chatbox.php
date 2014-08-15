<?php

/**
 * @author  chatwing
 * @package Chatwing_SDK
 */

namespace Chatwing;

use Chatwing\Encryption\Session;

class Chatbox extends Object
{
    /**
     * @var Api
     */
    protected $api;
    protected $key = null;
    protected $alias = null;
    protected $params = array();

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function getChatboxUrl()
    {
        $chatboxUrl = $this->api->getAPIServer() . '/' . $this->getKey();
        if (!empty($this->params)) {
            if (isset($this->params['custom_session']) && is_array($this->params['custom_session'])) {
                // build custom session here ?
                $session = new Session($this->params['custom_session']['secret']);
                unset($this->params['custom_session']['secret']);
                $this->params['custom_session'] = $session->toEncryptedSession();
            }
            $chatboxUrl .= '?' . http_build_query($this->params);
        }
        return $chatboxUrl;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setParam($key, $value = '')
    {
        if(is_array($key)) {
            foreach($key as $k => $v) {
                $this->setParam($k, $v);
            }
        } else {
            $this->params[$key] = $value;
        }
    }

    public function getParams()
    {
        return $this->params;
    }
} 