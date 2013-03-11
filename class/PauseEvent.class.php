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
  private $start,$end,$class;

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
     $start = number_format($this->start, 10, '.', '');
     //$end =   number_format((float)$this->start+(int)$this->duration, 10, '.', '');
     $end = $start+1;
     $duration = (int)$this->duration*1000;
     $eventsjs = "";
     foreach($this->aPauseSubEvent as $oPauseSubEvent)
     {
       $eventsjs .= $oPauseSubEvent->getJS();
     }
     $classstatement = $this->class?"$('body').attr('class','$this->class');":"";

   $this->js .= <<<EOF
     
    popcorn.code({
       start: $start,
       end: $end,
       onStart: function( options ) {
         playercmd('pause');
         $classstatement
         setTimeout(function() { 
           playercmd('play');
         }, $duration);
         $eventsjs 
       },
       onEnd: function( options ) {
       }
     });\n

EOF;

  }

  private function preprocess()
  {
    $this->start = $this->conf['start'];
    $this->duration = $this->conf['duration'];
    $this->class = isset($this->conf['class'])?$this->conf['class']:"";
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
