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
     $eventsjs = "";
     foreach($this->aPauseEvent as $oPauseEvent)
     {
       $eventsjs .= $oPauseEvent->getJS();
     }
     
   $this->js .= <<<EOF
     
    popcorn.code({
       start: $start,
       end: $end,
       onStart: function( options ) {
         $('body').attr('class','$this->template');
         playercmd('pause');
         setTimeout(function() { playercmd('play'); }, $duration);
         $eventsjs 
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
    $pause_events = isset($this->conf['pauseEvents'])?$this->conf['pauseEvents']:array();

    foreach($pause_events as $pause_event)
    {
      $eventClassname = ucfirst($pause_event['type'])."PauseEvent";
      if (class_exists($eventClassname))
      {
         $this->aPauseEvent[] = new $eventClassname($pause_event);
      } 
    }

    return true;
  }

  public function getJS()
  {
    return $this->js;
  }

}


?>
