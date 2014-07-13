<?php
/**
 * @author chatwing
 *
 */

namespace Chatwing;

use \Chatwing\Exception\ChatwingException;

class Object
{
    protected $_data = array();

    public function __construct($data = array())
    {
        if(!empty($data)) {
            $this->setData($data);
        }
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_data = array_merge($this->_data, $key);
        } else {
            $this->_data[$key] = $value;
        }
    }

    public function getData($key, $default = null)
    {
        return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
    }

    public function __call($name, $argument)
    {
        switch (substr($name, 0, 3)) {
            case 'set':
                $key = $this->_makeUnderscoreString(substr($name, 3));
                $this->setData($key, isset($argument[0]) ? $argument[0] : null);
                break;

            case 'get':
                $key = $this->_makeUnderscoreString(substr($name, 3));
                $this->getData($key, isset($argument[0]) ? $argument[0] : null);
                break;

            default:
                throw new ChatwingException(array('message' => "Method not found"));
        }

    }

    /**
     * Convert from camel-case string to underscore lowercase string
     * Eg: CamelCase => camel_case
     *
     * @param $str string
     *
     * @return mixed
     */
    protected function _makeUnderscoreString($str)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $str));
    }
} 