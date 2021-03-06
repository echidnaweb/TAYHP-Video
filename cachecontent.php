<?php
header('Content-Type: text/javascript; charset=UTF-8');
// Define Constants
define("TWITTER_MAX_RESULTS",100);
define("TWITTER_URL", "http://search.twitter.com/search.json?rpp=".TWITTER_MAX_RESULTS);
define("TWITTER_CACHE_EXPIRY", 3600);
define("FLICKR_CACHE_EXPIRY", 3600);
define("FLICKR_API_KEY", "b9f54a73e2502555f2f88cae4461e70f");
define("CONFIG_PATH", dirname(__FILE__)."/conf");
define("CACHE_DIR", dirname(__FILE__)."/cache");

// Includes
include ("class/PopcornProduction.class.php");
include ("class/TwitterAPI.class.php");
include ("class/TwitterEvent.class.php");
include ("class/TextEvent.class.php");
include ("class/FlickrEvent.class.php");
include ("class/GooglemapEvent.class.php");
include ("class/WordriverEvent.class.php");
include ("class/HideMediaEvent.class.php");
include ("class/PauseMediaEvent.class.php");
include ("class/ChangeTemplateEvent.class.php");
include ("class/ExtDataCache.class.php");
include ("lib/phpFlickr-3.1/phpFlickr.php");

array_map('unlink', glob(CACHE_DIR."/*.cache"));
array_map('unlink', glob(CACHE_DIR."/*.json"));


$files = scandir(CONFIG_PATH);

foreach ($files as $file)
{
  if (pathinfo($file, PATHINFO_EXTENSION)=="json" && substr($file,0,1) != ".")
  {
     echo "caching content for: ".$file."\n";
     $pcp = new PopCornProduction(CONFIG_PATH."/".$file);
  }
}


//echo "-->".CACHE_DIR."/*.cache";
//chmod(CACHE_DIR."/*.cache", 0777);

?>
