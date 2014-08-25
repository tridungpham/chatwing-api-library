<?php
/**
 * @author chatwing
 * @package Chatwing_SDK
 */

namespace Chatwing\Encryption;

use Chatwing\Object;
use Chatwing\Exception\ChatwingException;

class Session extends Object
{
    private $secret = '';

    const BLOCK_SIZE = 16;

    public function __construct($secret = '')
    {
        if($secret) {
            $this->setSecret($secret);
        }
    }

    public function setSecret($str)
    {
        $this->secret = $str;
        return $this;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return array
     * @throws \Chatwing\Exception\ChatwingException
     */
    protected  function getKeyAndIv()
    {
        $secret = $this->getSecret();
        if(!$secret) {
            throw new ChatwingException(array('message' => 'Secret has not been set !!'));
        }
        $md5Secret     = md5($this->getSecret());
        $encryptionKey = substr($md5Secret, 0, 16);
        $iv            = substr($md5Secret, 16, 16);
        return array($encryptionKey, $iv);
    }

    /**
     * @return string
     */
    public function toEncryptedSession()
    {
        list($encryptionKey, $iv) = $this->getKeyAndIv();
        $data = json_encode($this->_data);
        $pad  = self::BLOCK_SIZE - (strlen($data) % self::BLOCK_SIZE);
        $data .= str_repeat(chr($pad), $pad);

        return $encryptedSession = bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, $data, MCRYPT_MODE_CBC, $iv));
    }

    /**
     * @param string $encryptedSession
     *
     * @return array|mixed
     */
    public function toOriginalData($encryptedSession = '')
    {
        list($encryptionKey, $iv) = $this->getKeyAndIv();
        $result = array();
        if(!$encryptedSession) {
            return $result;
        }

        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, hex2bin($encryptedSession), MCRYPT_MODE_CBC, $iv);
        return json_decode(trim($data), true);
    }

    function __toString()
    {
        return $this->toEncryptedSession();
    }

}