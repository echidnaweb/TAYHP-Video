<?php
// Define Constants
define("TWITTER_MAX_RESULTS",100);
define("TWITTER_URL", "http://search.twitter.com/search.json?rpp=".TWITTER_MAX_RESULTS);
define("TWITTER_MAX_CALLS", 100);
define("CONFIG_PATH", getcwd()."/conf");
define("CACHE_FILE_PATH", getcwd()."/cache/extdata.json");

// Define Globals
$aTL= array(); //timeline data 
$aExtDataCache = new ExtDataCache(CACHE_FILE_PATH,3600); //external data cache
$APICalls = array();

// Attempt to load timeline data
if (!isset($_GET['video']))
  $tljson = loadtljson($_GET['video']);
else
{
  echo "Video ID must be supplied in URL"; 
  exit();
}

// Decode json to PHP array
$aTL=json_decode($tljson,true);

// Trim timeline data array
trimtldata($aTL);

// Look up external data
lookupextdata($aTL);

print_r($aTL);
exit();

$aQuery = array (    1 => "blue meanies",
                    15 => "yellow submarine",
                    25 => "turnips");
$results = array();
foreach ($aQuery as $time => $query)
{
  $aQuery[$time] = urlencode($query); 
}

foreach ($aQuery as $time => $query)
{
  $json = file_get_contents(URL."&q=".$query);
  $decode = json_decode($json, true);
  $results[] = array( "from_user" => $decode['results'][0]['from_user'],
                      "text" => $decode['results'][0]['text'] );
}

echo "<pre>";
print_r(json_encode($results));
echo "</pre>";

function loadtljson($videoid)
{
  if(!$tljson = file_get_contents(CONFIG_PATH."/".$videoid.".json")
  {
    echo "Error loading timeline data";
    exit();
  }
  return $tljson; 
}

function trimtldata(&$aTL)
{
  $aTL = $aTL['media'][0];
  $aTL['events'] = array();
  //no need for separate 'tracks'
  foreach ($aTL['tracks'] as $track)
  {
    foreach ($track['trackEvents'] as $trackevent)
    {
      $aTL['events'][] = $trackevent;
    }
  }
  unset($aTL['tracks']);
}

/* look up event-specific external data via web services etc */
function lookupextdata(&$aTL)
{
  foreach ($aTL['events'] as $event)
  {
    switch($event['type'])
    {
      case "twitter":
        $event['popcornOptions']['text'] = lookuptweet($event['popcornOptions']['src']); 
        break;
      case "flickr":
        break;
    } 
  }
}

function twtAPISearch($query)
{
  return "some text":
}

function getcachevalue($type,$key)
{
  if (isset
}
?>
