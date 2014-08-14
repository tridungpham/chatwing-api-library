<?php
/**
 * @author  chatwing
 * @package Chatwing_SDK
 */

namespace Chatwing;

class Chatbox extends Object
{
    protected $api;
    protected $key = null;
    protected $alias = null;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function getChatboxUrl()
    {

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
} 