<?php

namespace Gossamer\Caching;


use Gossamer\Caching\CachingInterface;


/**
 * Description of CacheManager
 *
 * @author Dave Meikle
 */
class CacheManager implements CachingInterface{
   
    protected $MAX_FILE_LIFESPAN = 1200;
    
    
    public function __construct(array $params = null) {
        if(!is_null($params)) {
            if(array_key_exists('MAX_FILE_LIFESPAN', $params)) {
                $this->MAX_FILE_LIFESPAN = $params['MAX_FILE_LIFESPAN'];
            }
        }
    }
    
    public function retrieveFromCache($key) {
      if(file_exists(__CACHE_DIRECTORY . "$key.cache") && $this->isNotStale(__CACHE_DIRECTORY . "$key.cache")) {
            
            $loadedValues = include __CACHE_DIRECTORY . "$key.cache";
            return $loadedValues;            
        }
        
        return false;
    }
    
    private function isNotStale($filepath) {
        $filetime = filemtime($filepath);
        $currentTime = time();
        
        return ($currentTime - $filetime) < $this->MAX_FILE_LIFESPAN;
    }

    public function saveToCache($key, $params) {
        if($this->inDogpileMode($key)) {
            //someone is already writing to the file so we cannot cache right now
            return;
        }
        
        try{
            //first save current cache to dogpile file
            $this->createDogpileFile($key);
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
        copy(__CACHE_DIRECTORY . "$key.cache", __CACHE_DIRECTORY . "$key.dogpile.cache");
    }
    
    protected function deleteDogpileFile($key) {
        unlink(__CACHE_DIRECTORY . "$key.dogpile.cache");
    }
    
    protected function inDogpileMode($key) {
        return file_exists(__CACHE_DIRECTORY . "$key.dogpile.cache");
    }
    
    
    private function formatValuesBeforeSaving($values) {
        if(is_array($values)) {
            return "<?php\r\n"
            . "return " . $this->parseArray($values) . ";";
        }
        
        return $values;
    }
    
    private function parseArray(array $values) {
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
