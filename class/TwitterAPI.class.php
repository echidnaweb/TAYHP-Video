<?php
/**
 * A class for executing calls to the twitter API 
 *
 **/

class TwitterAPI
{
  private static $cache;

  /* Process a Popcorn Twitter event */
  public function processEvent(&$event,&$tweets)
  {
    $results = array();
    $occurrences = isset($event['occurrences'])?(int)$event['occurrences']:1;
    if(isset($event['popcornOptions']['src']))
      $qry = $event['popcornOptions']['src'];
    else if (isset($event['src']))
      $qry = $event['src'];
    else return false;
    
    // if there is an entry in the cache retrieve it
    $results = $this->getCache()->getValue($qry);
    // otherwise search the Twitter API 
    if (!$results || count($results) == 0) 
    {
      $results = $this->doSearch($qry);
      $this->getCache()->setValue($qry,$results);
      if ($results && count($results) > 0) $this->getCache()->save();
    }
    // Pick a random result and set the text node to its value 
    if ($results && count($results) > 0)
    {
      $keys = array_rand($results,$occurrences); 
      if (is_array($keys))
        foreach ($keys as $key) $tweets[] = $results[$key];
      else
        $tweets[] = $results[$keys];
    } 
    else return false;
    
    return true;
  }

  public function doCachedSearch($qry)
  {
    $results = array();
    // if there is an entry in the cache retrieve it
    $results = $this->getCache()->getValue($qry);
    // otherwise search the Twitter API
    if (!$results || count($results) == 0)
    {
      $results = $this->doSearch($qry);
      $this->getCache()->setValue($qry,$results);
      if ($results && count($results) > 0) $this->getCache()->save();
    }
    return $results;
  }
 
  /* Search the Twitter API for a query string */
  private function doSearch($qry)
  {
    $results = array();
    // Call the Twitter API (unauthenticated)
    $json = file_get_contents(TWITTER_URL."&q=".urlencode($qry)."+exclude:retweets");
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
