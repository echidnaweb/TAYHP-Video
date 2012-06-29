/**
 * A class for executing calls to the twitter API 
 *
 **/

class TwitterAPI
{
  private $cache;

  function __construct()
  {
    // load up the cache 
    $cache_dir = defined(CACHE_DIR)?CACHE_DIR."/twitter.json":"/tmp/twitter.json");
    $cache_expiry = defined(TWITTER_CACHE_EXPIRY)?CACHE_DIR."/twitter.json":3600;
    $this->cache = new ExtDataCache($cache_dir,$cache_expiry);
  }

  function processEvent($event)
  {
    if(isset($event['popcornOptions']['src']))
    {
      if (!$result = $cache->getValue($event['popcornOptions']['src']) )
      {
        
      } 

    }
  }

  function doSearch($qry)
  {
    $url = defined(TWITTER_URL)?TWITTER_URL:"http://search.twitter.com/search.json?rpp=100";
    // Call the Twitter API (unauthenticated)
    $json = file_get_contents($url."&q=".urlencode($qry));
      
    // Did it work?
    if (!$json) return array();
          
    // Decode json to PHP array
    $decode = json_decode($json, true);
              
    // Make a simple assoc array from the results
    $results[] = array( "from_user" => $decode['results'][0]['from_user'],
                        "text" => $decode['results'][0]['text'] );
    return $results; 
  }
}
