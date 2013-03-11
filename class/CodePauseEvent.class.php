<?php
/**
 * CodePauseEvent class
 * Executes arbitrary javascript during a pause 
 * Generates javascript code from config array 
 *
 **/

class CodePauseEvent
{
  private $conf;
  private $js = "";

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    $this->startjs = $this->startjs;
    $this->endjs = $this->endjs;

    for($i = 0; $i < $this->occurrences; $i++)
    {
       $interval = $this->interval*1000;
       $delay = $this->delay*1000;
       $start = $delay+($interval*$i);
       $duration = $this->duration*1000;
       $end = $start+$duration;

       $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
   
       $this->js .= <<<EOF

         setTimeout(function() {
           $classstatement
           $this->startjs
         }, $start);
         
         setTimeout(function() {
           $this->endjs
         }, $end);     

EOF;

     } 
  }

  private function preprocess()
  {
    $this->startjs = $this->conf['startjs'];
    $this->endjs = $this->conf['endjs'];
    $this->class = isset($this->conf['class'])?$this->conf['class']:false;
    $this->occurrences = isset($this->conf['occurrences'])?(int)$this->conf['occurrences']:1;
    $this->interval = isset($this->conf['interval'])?(int)$this->conf['interval']:5; 
    $this->delay = isset($this->conf['delay'])?$this->conf['delay']:0;
    $this->duration = isset($this->conf['duration'])?$this->conf['duration']:5;

    return true;
  }

  public function getJS()
  {
    return $this->js;
  }

}


?>
