<?php
/**
 * ChangeClassPauseEvent class
 * Changes the value of the class attribute of the body tag during a pause 
 * Generates javascript code from config array 
 *
 **/

class ChangeClassPauseEvent
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
     $delay = $this->delay*1000;

     $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
   
     $this->js .= <<<EOF

         pause_event_timer.push(setTimeout(function() {
           $classstatement
         }, $delay));
EOF;

  }

  private function preprocess()
  {
    $this->class = isset($this->conf['class'])?$this->conf['class']:false;
    $this->delay = isset($this->conf['delay'])?$this->conf['delay']:0;

    return true;
  }

  public function getJS()
  {
    return $this->js;
  }

}


?>
