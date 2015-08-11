<?php

namespace test\Gossamer\Caching;

use Gossamer\Caching\CacheManager;
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
    
    public function testHTMLSaveToCache() {
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->saveToCache('html', '<div id="test">this is a test</div>',true);
        $this->assertTrue($result);        
    }
    
    public function testHTMLRetrieveFromCache() {
        
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->retrieveFromCache('html', true);
       
        $this->assertEquals($result, '<div id="test">this is a test</div>');
    }
    
    public function testSaveToCacheSubfolder() {
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->saveToCache('/subfolder/testing', array('marco' => 'polo'));
        $this->assertTrue($result);          
    }
    public function testRetrieveFromCacheSubfolder() {
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->retrieveFromCache('/subfolder/testing');
       
        $this->assertTrue(array_key_exists('marco', $result));
        $this->assertEquals($result['marco'], 'polo'); 
    }
    
    public function testDeleteCache() {
        $params = array('MAX_FILE_LIFESPAN' => 10); //10 seconds
        $mgr = new CacheManager($this->getLogger());
        
        $result = $mgr->saveToCache('/subfolder/testing', array('marco' => 'polo'));
        
        $mgr->deleteCache('/subfolder/testing');
    }
}
