chatwing-api-library
====================

PHP Library which is used to interact with Chatwing API
Usage:

```php

require_once 'vendor/autoload.php'; //require composer autoloader

$key = "YOUR_ACESS_KEY";
$clientId = "YOUR_CLIENT_ID";

try{
    $api = new Chatwing\Api($key, $clientId);
    $api->setEnv(Chatwing\Api::ENV_DEVELOPMENT);
    $r = $api->call('user/chatbox/list',array('test1' => 10, 'test2' => 100));    
} catch(Exception $e){
    die($e->getMessage());
}
```