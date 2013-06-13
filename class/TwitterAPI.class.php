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
    if(isset($event['src']))
      $qry = $event['src'];
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
      if ($occurrences > count($results)) $occurrences = count($results);
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
    print_r($results);
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

    $settings = array(
    'oauth_access_token' => TWITTER_OAUTH_ACCESS_TOKEN,
    'oauth_access_token_secret' => TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
    'consumer_key' => TWITTER_CONSUMER_KEY,
    'consumer_secret' => TWITTER_CONSUMER_SECRET);

    //$getfield = "?q=test&include_rts=0&rpp=".TWITTER_MAX_RESULTS;
    $getfield = "?q=".rawurlencode($qry)."&include_rts=0&rpp=".TWITTER_MAX_RESULTS;    

    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange($settings);
    $json = $twitter->setGetfield($getfield)
             ->buildOauth(TWITTER_URL, $requestMethod)
             ->performRequest();

    if (!$json) return array();
          
    $returndata = json_decode($json, true);
    foreach ($returndata['statuses'] as $rawresult)
    {
      $results[] = array( "from_user" => "@".$rawresult['user']['screen_name'],
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
