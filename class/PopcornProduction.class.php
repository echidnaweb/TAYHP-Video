<?php
/**
 * A class for generating Popcorn.js javascript event calls from a json-based configuration 
 *
 *
 **/

Class PopcornProduction
{
  private $aConfig;
  private $aError;
  function __construct($pathtoconfig)
  {
     $this->loadConfig($pathtoconfig);
     $this->process(); 
  }
  
  public function getJS()
  {
    return "alert('JS loaded!');\n";
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

  private function process()
  {
     foreach ($this->aConfig['media'][0]['tracks'] as $track)
     {
       foreach ($track['trackEvents'] as $event)
       {
         $this->processEvent($event); 
       }
     } 
  }
  
  private function processEvent(&$event)
  {
     if (isset($event['type']))
     {
        $APIClassname = ucfirst($event['type'])."API";
        if (class_exists($APIClassname)) 
        {
          $oAPI = new $APIClassname;
          $oAPI->processEvent($event);
        }
     } 
  }

  private function addError($errstr)
  {
    $this->aError[] = $errstr; 
  }
  
}
