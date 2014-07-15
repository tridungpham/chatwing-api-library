<?php

namespace Chatwing\Api;

use \Chatwing\Exception\ChatwingException;

class Response extends \ArrayObject
{
    public function __construct($data = array())
    {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                $value = new static($value);
            }
            $this->offsetSet($key, $value);
        }

        $this->setFlags(self::ARRAY_AS_PROPS);
    }

    function __toString()
    {
        return json_encode($this);
    }

}