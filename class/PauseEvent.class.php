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
     foreach($this->aPauseSubEvent as $oPauseSubEvent)
     {
       $eventsjs .= $oPauseSubEvent->getJS();
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
    $this->duration = $this->conf['duration'];
    $this->template = isset($this->conf['template'])?$this->conf['template']:"";
    $pause_sub_events = isset($this->conf['pauseSubEvents'])?$this->conf['pauseSubEvents']:array();

    foreach($pause_sub_events as $pause_sub_event)
    {
      $eventClassname = ucfirst($pause_sub_event['type'])."PauseEvent";
      if (class_exists($eventClassname))
      {
         $this->aPauseSubEvent[] = new $eventClassname($pause_sub_event);
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
