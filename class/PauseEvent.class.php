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
     $end =   (int)$this->start+1;
     $duration = $this->duration;
       
   $this->js .= <<<EOF
    // Create a popcorn event
    popcorn.pause({
       start: $start,
       end: $end,
       duration: $duration
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
