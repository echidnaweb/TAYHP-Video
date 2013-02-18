<?php
/**
 * PauseMediaEvent class
 * Event to pause media in popcorn production 
 * Generates javascript code from config array 
 *
 **/

class PauseMediaEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$duration;

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    $this->js .= <<<EOF

    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       onStart: function( options ) {
         window.popcorn.pause(10); 
       },
       onEnd: function( options ) {
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->id = isset($this->conf['id'])?$this->conf['id']:"0";
    $this->start = isset($this->conf['start'])?$this->conf['start']:"0";
    $this->duration = isset($this->conf['duration'])?$this->conf['duration']:"0";

    return true;
  }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
