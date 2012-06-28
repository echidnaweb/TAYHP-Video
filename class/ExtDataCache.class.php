/**
 * External Data Cache 
 * 
 * Saves arbitrary values to and retrieves them from a text file 
 *
 **/

class ExtDataCache {
    
    private $cache = array();
    private $path;
    private $expiry;

    function __construct($path,$expiry)
    {
      $this->path = $path;
      $this->expiry = $expiry; 
      $this->load();
    }
    
    /* Load cache from file */    
    private function load()
    {
       if (!@file_exists($this->path))
       {
         return true;
       }
       
       if (filemtime($path) < (time() - $expiration))
       {
         $this->purge();
         return true;
       }
 
       if (!$fp = @fopen($path, 'rb'))
       {
         return true;
       }

       flock($fp, LOCK_SH);

       if (filesize($cache_path) > 0)
       {
         $this->cache = unserialize(fread($fp, filesize($path)));
       }

       flock($fp, LOCK_UN);
       fclose($fp);
    }
    
    private function purge()
    {
    }
    
    public function save()
    {

    } 

    public function setValue($key,$value)
    {

    }

    public function getValue($key,$value)
    {

    }
}
