<?php
/**
 * A class for generating Popcorn.js javascript event calls from a json-based configuration 
 *
 *
 **/

Class PopcornProduction
{
  private $aConfig;
  private $aEvent;
  private $aTarget;
  private $aError;
  private $js;

  function __construct($pathtoconfig)
  {
     $this->loadConfig($pathtoconfig);
     $this->loadEvents();
     $this->generateJS();
  }
  
  public function getJS()
  {
    return $this->js;
  } 

  private function generateJS()
  {
    $media_url = $this->aConfig['media'][0]['url'][0];
    $this->js = <<<EOF
function init_popcorn()
{
    var popcorn = Popcorn.vimeo( "#video", "$media_url");

EOF;

    foreach ($this->aEvent as $event)
    {
      $this->js .= $event->getJS();
    }
    $this->js .="}\n";

  }
  
  /* Load and decode the JSON config from the specified path */
  private function loadConfig($pathtoconfig)
  {
    if($json = file_get_contents($pathtoconfig))
    {
      if ($decode = json_decode($json,true))
      {
        $this->aConfig = $decode;
      }
      else
      {
        $this->addError("Could not decode JSON config");
        return false;
      }
    }
    else
    {
       $this->addError("Could not load JSON config");
       return false;
    }
    return true;
  }

  private function loadEvents()
  {
    foreach ($this->aConfig['media'][0]['tracks'] as $track)
    {
       foreach ($track['trackEvents'] as $event)
       {
         if (isset($event['type']))
         {
            $eventClassname = ucfirst($event['type'])."Event";
            if (class_exists($eventClassname))
            {
              $this->aEvent[] = new $eventClassname($event);
            }
          }
       }
    }
  }

  private function addError($errstr)
  {
    $this->aError[] = $errstr; 
  }
  
}
