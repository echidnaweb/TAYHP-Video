<?php
// Define Constants
define("URL", "http://search.twitter.com/search.json?rpp=1");
define("CONFIG_PATH", getcwd()."/conf");
define("FOO_BAR", "something more");

// Check that video ID has been passed in GET string
if (!isset($_GET['video']))
{
  echo "Video ID must be supplied in URL"; 
  exit();
}

// Attempt to load timeline data
if(!$tljson = file_get_contents(CONFIG_PATH."/".$_GET['video'].".json"))
{
  echo "Error loading timeline data";
  exit();
}  

// Decode json to a PHP array 
$aTL=json_decode($tljson,true);

// Trim timeline data array
trimtldata($aTL);

lookuptweets($aTL);

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

function lookuptweets(&$aTL)
{

}
?>
