<?php

namespace Gossamer\Caching;


use Gossamer\Caching\CachingInterface;
use Monolog\Logger;


/**
 * Description of CacheManager
 *
 * @author Dave Meikle
 */
class CacheManager implements CachingInterface{
   
    protected $MAX_FILE_LIFESPAN = 1200;
    
    protected $MAX_WRITE_TIME_ELAPSED = 60;
    
    protected $logger = null;
    
    public function __construct(Logger $logger, array $params = null) {
        $this->logger = $logger;
        if(!is_null($params)) {
            if(array_key_exists('MAX_FILE_LIFESPAN', $params)) {
                $this->MAX_FILE_LIFESPAN = $params['MAX_FILE_LIFESPAN'];
            }
        }
    }
    
    public function retrieveFromCache($key) {
      if(file_exists(__CACHE_DIRECTORY . "$key.cache") && $this->isNotStale(__CACHE_DIRECTORY . "$key.cache", $this->MAX_FILE_LIFESPAN)) {
            
            $loadedValues = include __CACHE_DIRECTORY . "$key.cache";
            return $loadedValues;            
        }
        
        return false;
    }
    
    private function isNotStale($filepath, $decayTime) {
        $filetime = filemtime($filepath);
        $currentTime = time();
        
        return ($currentTime - $filetime) < $decayTime;
    }

    
    public function saveToCache($key, $values) {
        if($this->inDogpileMode($key)) {
            //check to see if we're in a stale write condition
            if($this->isNotStale(__CACHE_DIRECTORY . "$key.cache.dogpile", $this->MAX_WRITE_TIME_ELAPSED)) {
                //someone is already writing to the file so we cannot cache right now
                return;
            }
            //seems to be stale - shouldn't have taken this long to create
        }
        
        try{
            //first save current cache to dogpile file
            $this->createDogpileFile($key);
            $this->logger->addDebug('Caching - saving values to cache file');
            $file = fopen(__CACHE_DIRECTORY . "$key.cache", "w") or die("Unable to open file!");
        }  catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
           
            return false;
        }
        
        fwrite($file, $this->formatValuesBeforeSaving($values));
        fclose($file);
        
        $this->deleteDogpileFile($key);
        
        return true;
    }

    protected function createDogpileFile($key) {
        $this->logger->addDebug('Caching - creating shunt for dogpile condition');
        if(!file_exists(__CACHE_DIRECTORY . "$key.cache")) {
            touch(__CACHE_DIRECTORY . "$key.cache.dogpile");
            
            return;
        }
        copy(__CACHE_DIRECTORY . "$key.cache", __CACHE_DIRECTORY . "$key.cache.dogpile");
    }
    
    protected function deleteDogpileFile($key) {
        $this->logger->addDebug('Caching - deleting shunt for dogpile condition');
        unlink(__CACHE_DIRECTORY . "$key.cache.dogpile");
    }
    
    protected function inDogpileMode($key) {
        $this->logger->addDebug('Caching - checking for dogpile condition');
        if(file_exists(__CACHE_DIRECTORY . "$key.cache.dogpile")) {
            $this->logger->addDebug('Caching - currently in dogpile condition');            
        }
        
        return file_exists(__CACHE_DIRECTORY . "$key.cache.dogpile");
    }
    
    
    private function formatValuesBeforeSaving($values) {
        $this->logger->addDebug('Caching - formatting values before saving');
        
        if(is_array($values)) {
            return "<?php\r\n"
            . "return " . $this->parseArray($values) . ";";
        }
        
        return $values;
    }
    
    private function parseArray(array $values) {
        $this->logger->addDebug('Caching - parsing array values');
        $retval = "array (";
        $elements = '';
        foreach($values as $key => $row) {
            if(is_array($row)) {
                $elements .= ",\r\n'$key' => " . $this->parseArray($row) ;
            }else{
               $elements .= ",\r\n'$key' => '$row'"; 
            }            
        }
        $retval .= substr($elements, 1) . ")";
        
        return $retval;
    }
}
