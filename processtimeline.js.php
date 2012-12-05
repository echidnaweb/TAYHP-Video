<?php
header('Content-Type: text/javascript; charset=UTF-8');
ini_set('display_errors',1); 
 error_reporting(E_ALL);
// Define Constants
define("TWITTER_MAX_RESULTS",100);
define("TWITTER_URL", "http://search.twitter.com/search.json?include_rts=0&rpp=".TWITTER_MAX_RESULTS);
define("TWITTER_CACHE_EXPIRY", 3600);
define("FLICKR_PER_PAGE", 200);
define("FLICKR_CACHE_EXPIRY", 3600);
define("FLICKR_API_KEY", "b9f54a73e2502555f2f88cae4461e70f");
define("CONFIG_PATH", getcwd()."/conf");
define("CACHE_DIR", getcwd()."/cache");

// Includes
include ("class/PopcornProduction.class.php");
include ("class/TwitterAPI.class.php");
include ("class/TwitterEvent.class.php");
include ("class/TwitterPauseEvent.class.php");
include ("class/TextEvent.class.php");
include ("class/FlickrEvent.class.php");
include ("class/GooglemapEvent.class.php");
include ("class/WordriverEvent.class.php");
include ("class/HideMediaEvent.class.php");
include ("class/PauseEvent.class.php");
include ("class/ChangeTemplateEvent.class.php");
include ("class/ExtDataCache.class.php");
include ("lib/phpFlickr-3.1/phpFlickr.php");


if (isset($_GET['video']))
{
   $pcp = new PopCornProduction(CONFIG_PATH."/".$_GET['video'].".json");
   $output = $pcp->getJS();
}
else $output = "alert('Error: Missing Video ID!');";


echo $output;

?>
