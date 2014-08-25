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
    protected $secret = null;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * [getChatboxUrl description]
     *
     * @throws Exception\ChatwingException If no alias or chatbox key is set
     * @return string
     */
    public function getChatboxUrl()
    {
        $chatboxName = $this->getAlias() ? $this->getAlias() : $this->getKey();
        if (!$chatboxName) {
            throw new ChatwingException(array('message' => 'No chatbox key or alias defined!'));
        }

        $chatboxUrl = 'http://' . $this->api->getAPIServer() . '/' . $chatboxName;
        if (!empty($this->params)) {
            if($this->getSecret()) {
                $this->getEncryptedSession(); // call this method to create encrypted session
            }
            $chatboxUrl .= '?' . http_build_query($this->params);
        }
        return $chatboxUrl;
    }

    /**
     * [setKey description]
     *
     * @param [type] $key [description]
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * [getKey description]
     *
     * @return [type] [description]
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * [setAlias description]
     *
     * @param [type] $alias [description]
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * [getAlias description]
     *
     * @return [type] [description]
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * [setParam description]
     *
     * @param [type] $key   [description]
     * @param string $value [description]
     *
     * @return $this
     */
    public function setParam($key, $value = '')
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setParam($k, $v);
            }
        } else {
            $this->params[$key] = $value;
        }
        return $this;
    }

    public function getParam($key = '', $default = null)
    {
        if(empty($key)) {
            return $this->params;
        }
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * [getParams description]
     *
     * @return array [type] [description]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set chatbox secret key
     * @param $s
     *
     * @return $this
     */
    public function setSecret($s)
    {
        $this->secret = $s;
        return $this;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function getEncryptedSession()
    {
        if (isset($this->params['custom_session'])) {
            $customSession = $this->params['custom_session'];
            if (is_string($customSession)) {
                return $customSession;
            }

            if (is_array($customSession) && !empty($customSession) && $this->getSecret()) {
                $session = new Session();
                $session->setSecret($this->getSecret());
                $session->setData($customSession);
                $this->setParam('custom_session', $session->toEncryptedSession());

                return $this->getParam('custom_session');
            }

            unset($this->params['custom_session']);
        }

        return false;
    }
} 