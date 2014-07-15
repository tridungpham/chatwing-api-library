<?php
/**
 * Author: dphamtri
 */

namespace Chatwing\Api;

use \Chatwing\Object;
use \Chatwing\Exception\ChatwingException;

/**
 * Class Action
 *
 * @package Chatwing\Api
 * @method getType() string
 */
class Action extends Object
{
    private static $actionList = array();

    /**
     * Constructor of Action object. Throw exception if action is not found
     *
     * @param       $name
     * @param array $params
     *
     * @throws \Chatwing\Exception\ChatwingException
     */
    public function __construct($name, $params = array())
    {
        if (empty(self::$actionList)) {
            self::loadActionList();
        }

        $this->_setCurrent($name);
        $this->setData('params', $params);
    }

    public function toQueryUri()
    {
        return $this->getActionUri() . ($this->getType() == 'get' ? '?' . http_build_query($this->getData('params', array())) : '') ;
    }

    public function getActionUri()
    {
        return $this->getName();
    }

    public function isActionValid($actionName)
    {
        return isset(self::$actionList[$actionName]) && !empty(self::$actionList[$actionName]);
    }

    protected static function loadActionList($path = null)
    {
        if (is_null($path)) {
            if (!defined('CHATWING_BASE_DIR')) {
                define('CHATWING_BASE_DIR', dirname(dirname(__FILE__)));
            }
            $path = dirname(CHATWING_BASE_DIR) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'actions.php';
        }
        if (file_exists($path)) {
            self::$actionList = include $path;
        } else {
            throw new ChatwingException(array('message' => "Action list not found", 'code' => 0));
        }
    }

    private function _setCurrent($actionName)
    {
        if (!$this->isActionValid($actionName)) {
            throw new ChatwingException(array('message' => "Invalid action", 'code' => 0));
        }
        $this->setName($actionName);
        foreach (self::$actionList[$actionName] as $key => $value) {
            $this->setData($key, $value);
        }
    }
} 