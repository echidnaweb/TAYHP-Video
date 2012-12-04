<?php
/**
 * PauseEvent class
 * Represents a single twitter event on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class PauseEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end,$template;

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
     $start = $this->start;
     $end =   (int)$this->start+(int)$this->duration;
     $duration = (int)$this->duration*1000;
       
   $this->js .= <<<EOF
     
    popcorn.code({
       start: $start,
       end: $end,
       onStart: function( options ) {
         playercmd('pause');
         setTimeout(function() { playercmd('play'); }, $duration);
       },
       onEnd: function( options ) {
       }
     });\n

EOF;

  }

  private function preprocess()
  {
    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->duration = $this->conf['popcornOptions']['duration'];
    $this->template = isset($this->conf['template'])?$this->conf['template']:"";
    $this->pauseEvents = isset($this->conf['pauseEvents'])?$this->conf['pauseEvents']:array();
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }

}


?>
