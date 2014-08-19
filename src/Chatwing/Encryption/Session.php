<?php
/**
 * @author chatwing
 * @package
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
    }

    public function getSecret()
    {
        return $this->secret;
    }

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

    public function toEncryptedSession()
    {
        $data = json_encode($this->_data);
        $pad  = self::BLOCK_SIZE - (strlen($data) % self::BLOCK_SIZE);
        $data .= str_repeat(chr($pad), $pad);

        list($encryptionKey, $iv) = $this->getKeyAndIv();
        return $encryptedSession = bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, $data, MCRYPT_MODE_CBC, $iv));
    }

    public function toOriginalData($encryptedSession = '')
    {
        $result = array();
        if(!$encryptedSession) {
            return $result;
        }

        list($encryptionKey, $iv) = $this->getKeyAndIv();
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, hex2bin($encryptedSession), MCRYPT_MODE_CBC, $iv);
        return json_decode(trim($data), true);
    }

    function __toString()
    {
        return $this->toEncryptedSession();
    }

}