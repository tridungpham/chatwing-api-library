<?php
/**
 * @author  chatwing
 * @package Chatwing_Exception
 */

namespace Chatwing\Exception;

class ChatwingException extends \Exception
{
    protected $httpCode = 0;
    protected $params = array();

    function __construct($errorData = array(), $httpCode = 0)
    {
        $message = isset($errorData['message']) ? $errorData['message'] : '';
        $code = isset($errorData['code']) ? $errorData['code'] : 0;
        parent::__construct($message, $code, null);

        if(isset($errorData['params'])){
            $this->params = $errorData['params'];
        }
        if($httpCode) {
            $this->httpCode = $httpCode;
        }
    }

    public function getParams()
    {
        return $this->params;
    }
} 