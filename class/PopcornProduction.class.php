<?php
/**
 * A class for generating Popcorn.js javascript event calls from a json-based configuration 
 *
 *
 **/

Class PopcornProduction
{
  private $aConfig;
  private $aEvent = array();
  private $aTarget;
  private $aError;
  private $js;

  function __construct($pathtoconfig)
  {
     if ($this->loadConfig($pathtoconfig))
     {
       $this->loadEvents();
       $this->generateJS();
     }
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
    $('#video').empty();
    $('#videolayer').css('top', '-600px');
    $('.target').empty();
    popcorn = Popcorn.vimeo( "#video", "$media_url");
    popcorn.on( "loadedmetadata", function() { $('#attrib').empty(); this.play(); });

EOF;

    foreach ($this->aEvent as $event)
    {
      $this->js .= $event->getJS();
    }
    $this->js .="window.popcorn.play();";
    $this->js .="}\n";
    
  }
  
  /* Load and decode the JSON config from the specified path */
  private function loadConfig($pathtoconfig)
  {
    if (!file_exists($pathtoconfig))
    {
       $this->addError("Could not load JSON config");
       return false;
    }
 
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
