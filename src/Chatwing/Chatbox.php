<?php

/**
 * @author  chatwing
 * @package Chatwing_SDK
 */

namespace Chatwing;

use Chatwing\Encryption\Session;
use Chatwing\Exception\ChatwingException;

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

    /**
     * [getChatboxUrl description]
     * @return [type] [description]
     */
    public function getChatboxUrl()
    {
        $chatboxName = $this->getAlias() ? $this->getAlias() : $this->getKey();
        if(!$chatboxName) {
            throw new ChatwingException(array('message' => 'No chatbox key or alias defined!'));
        }

        $chatboxUrl = $this->api->getAPIServer() . '/' . $chatboxName;
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

    /**
     * [setKey description]
     * @param [type] $key [description]
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * [getKey description]
     * @return [type] [description]
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * [setAlias description]
     * @param [type] $alias [description]
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * [getAlias description]
     * @return [type] [description]
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * [setParam description]
     * @param [type] $key   [description]
     * @param string $value [description]
     */
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

    /**
     * [getParams description]
     * @return [type] [description]
     */
    public function getParams()
    {
        return $this->params;
    }
} 