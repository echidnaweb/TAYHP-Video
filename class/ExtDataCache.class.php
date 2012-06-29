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
    private function load($acceptexpired)
    {
       // if cache file doesn't exist we'll probably create a new one later
       if (!@file_exists($this->path))
       {
         return false;
       }
      
       // if cache file is expired delete it 
       if (!$acceptexpired $this->isExpired())
       {
         return false;
       }

       // attempt to get a file handle 
       if (!$fp = @fopen($path, 'r'))
       {
         return false;
       }
       
       // acquire a shared lock (others may read but not write) 
       flock($fp, LOCK_SH);

       // decode the data 
       if (filesize($cache_path) > 0)
       {
          $cache = json_decode(fread($fp, filesize($path)),true);
          if (is_null($cache) || !$cache) return false;
          $this->cache = $cache; 
       }
       lock($fp, LOCK_UN);
       fclose($fp);
    }
    
    private function isExpired())
    {
      return (filemtime($this->path) < (time() - $this->expiry));
    }    
 
    /* delete the cache file */ 
    private function delete()
    {
        if (file_exists(this->cache))
        {
            unlink($this->cache);
            return TRUE;
        }
        return false;
    }
   
    /* save a new cache file */ 
    public function save()
    {
      // check that the containing directory exists 
      $dir = dirname($this->path);
      if (!is_dir($dir) OR !is_writable($dir))
      {
         return FALSE;
      }
     
      // attempt to get a file handle
      if (!$fp = fopen($cache_path, 'w'))
      {
        return FALSE;
      }
      
      if (flock($fp, LOCK_EX))
      {
        fwrite($fp, json_encode($this->cache));
        flock($fp, LOCK_UN);
      }
      else return FALSE;

      fclose($fp);
      @chmod($this->path, 0777);
      return TRUE;
    } 
    
    /* set a value in the cache */
    public function setValue($key,$value)
    {
       $this->cache[$key] = $value;
    }

    /* get a value from the cache */
    public function getValue($key,$value)
    {
      if (isset($this->cache[$key]))
        return $this->cache[$key];
      else
        return false;
    }
}
