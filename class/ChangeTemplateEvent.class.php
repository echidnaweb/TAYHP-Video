<?php
/**
 * ChangeTemplateEvent class
 * Event to change the template in popcorn production 
 * Generates javascript code from config array 
 *
 **/

class ChangeTemplateEvent
{
  private $conf;
  private $js = "";
  private $template,$start;

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
         $('body').attr('class','$this->template');
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->start = isset($this->conf['start'])?$this->conf['start']:"0";
    $this->template = isset($this->conf['template'])?$this->conf['template']:"0";

    return true;
  }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
