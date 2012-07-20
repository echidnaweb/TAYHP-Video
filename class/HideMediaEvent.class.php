<?php
/**
 * HideMediaEvent class
 * Event to hide media in popcorn production 
 * Generates javascript code from config array 
 *
 **/

class HideMediaEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end;

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
       end: $this->end,
       onStart: function( options ) {
         $('#videolayer').css('top', '600px');
       },
       onEnd: function( options ) {
         $('#videolayer').css('top', '-600px');
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->id = isset($this->conf['id'])?$this->conf['id']:"0";
    $this->start = isset($this->conf['popcornOptions']['start'])?$this->conf['popcornOptions']['start']:"0";
    $this->end = isset($this->conf['popcornOptions']['end'])?$this->conf['popcornOptions']['end']:"0";

    return true;
  }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
