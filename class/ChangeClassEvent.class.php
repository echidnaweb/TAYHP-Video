<?php
/**
 * ChangeClassEvent class
 * Event to change the body class in popcorn production 
 * Generates javascript code from config array 
 *
 **/

class ChangeClassEvent
{
  private $conf;
  private $js = "";
  private $class,$start;

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
         //alert('foo 0');
         $('body').attr('class','$this->class');
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->start = isset($this->conf['start'])?$this->conf['start']:"0";
    $this->class = isset($this->conf['class'])?$this->conf['class']:"0";

    return true;
  }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
