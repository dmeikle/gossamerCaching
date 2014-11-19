<?php

namespace test\Gossamer\DBFramework\Caching;

use Gossamer\DBFramework\Caching\CacheManager;
use tests\BaseTest;


/**
 * Description of CacheManagerTest
 *
 * @author Dave Meikle
 */
class CacheManagerTest extends BaseTest{
  
    public function testSaveToCache() {
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->saveToCache('testing', array('marco' => 'polo'));
        $this->assertTrue($result);        
    }
    
    public function testRetrieveFromCache() {
        
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->retrieveFromCache('testing');
        
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('marco', $result));
        $this->assertEquals($result['marco'], 'polo');
    }
}
