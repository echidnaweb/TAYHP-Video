<?php
// Define Constants
define("TWITTER_MAX_RESULTS",100);
define("TWITTER_URL", "http://search.twitter.com/search.json?rpp=".TWITTER_MAX_RESULTS);
define("TWITTER_CACHE_EXPIRY", 3600);
define("CONFIG_PATH", getcwd()."/conf");
define("CACHE_DIR", getcwd()."/cache");
// Includes
include ("class/PopcornProduction.class.php");
include ("class/TwitterAPI.class.php");
include ("class/ExtDataCache.class.php");

if (isset($_GET['video']))
{
   $pcpage = new PopCornProduction(CONFIG_PATH."/".$_GET['video'].".json");
   $output = $pcpage->getJS();
}
else $output = "alert('Error: Missing Video ID!');";

echo $output;
?>
