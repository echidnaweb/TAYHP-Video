<?php
/**
 * A class for executing calls to the twitter API 
 *
 **/

class TwitterAPI
{
  private static $cache;

  /* Process a Popcorn Twitter event */
  public function processEvent($event)
  {
    $results = array();
    if(isset($event['popcornOptions']['src']))
    {
      $qry = $event['popcornOptions']['src'];

      // if there is an entry in the cache retrieve it
      $results = $this->getCache()->getValue($qry);

      // otherwise search the Twitter API 
      if (!$results || count($results) == 0) 
      {
        echo "searching twitter";
        $results = $this->doSearch($qry);
        $this->getCache()->setValue($qry,$results);
        if ($results && count($results) > 0) $this->getCache()->save();
      }

      // Pick a random result and set the text node to its value 
      if ($results && count($results) > 0)
      {
        $event['popcornOptions']['tweet'] = $results[array_rand($results)]; 
      } 
      else return false;
    }
    else return false;

    return true;
  }
 
  /* Search the Twitter API for a query string */
  private function doSearch($qry)
  {
    $results = array();
    // Call the Twitter API (unauthenticated)
    $json = file_get_contents(TWITTER_URL."&q=".urlencode($qry));
      
    // Did it work?
    if (!$json) return array();
          
    // Decode json to PHP array
    $returndata = json_decode($json, true);
              
    // Make a simple assoc array from the results
    foreach ($returndata['results'] as $rawresult)
    {
      $results[] = array( "from_user" => $rawresult['from_user'],
                         "text" => $rawresult['text'] );
    }
    return $results; 
  }

  public static function getCache()
  {
    if (self::$cache == null)
    {
      self::$cache = new ExtDataCache(CACHE_DIR."/twitter.json",TWITTER_CACHE_EXPIRY);
    } 
    return self::$cache;
  }
}
