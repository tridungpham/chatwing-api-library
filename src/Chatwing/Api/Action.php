<?php
/**
 * Author: dphamtri
 */

namespace Chatwing\Api;

use \Chatwing\Object;
use \Chatwing\Exception;
//use \Chatwing\Exception\ChatwingException;

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
        if (is_null(self::$actionList)) {
            self::loadActionList();
        }

        $this->_setCurrent($name);

    }

    public function toQueryUri()
    {

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
            throw new \Chatwing\Exception\ChatwingException("Action list not found");
        }
    }

    private function _setCurrent($actionName)
    {
        if (!$this->isActionValid($actionName)) {
            throw new \Chatwing\Exception\ChatwingException("Invalid action");
        }
        $this->setName($actionName);
        foreach (self::$actionList[$actionName] as $key => $value) {
            $this->setData($key, $value);
        }
    }
} 