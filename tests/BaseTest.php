<?php

namespace tests;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    const GET = 'GET';
    
    const POST = 'POST';
    
    protected function getLogger() {
        
            $logger = new Logger('phpUnitTest');
            $logger->pushHandler(new StreamHandler("logs/phpunit.log", Logger::DEBUG));  
       
        
        return $logger;
    }
    
    public function setRequestMethod($method) {
        define("__REQUEST_METHOD", $method);
    }
    
    public function setURI($uri) {
        define('__URI', $uri);
        define("__REQUEST_URI", $uri . '/');
    }
    
    public function testBase() {
        
    }
}
