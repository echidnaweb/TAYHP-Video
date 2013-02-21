<?php
/**
 * HTML Event class
 * Writes arbitrary html to screen on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class HTMLEvent
{
  private $conf;
  private $js = "";
  private $start,$end,$target,$height,$width,$html,$class;

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    $id = uniqid("html_"); 
    $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
    $this->js .= <<<EOF
    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         var html = '<div id=\'$id\'>addslashes($this->html)</div>';
         $classstatement
         $('#$this->target').html(text);
         $('#$this->target div#$id').fadeTo(1000, 0.999);
       },
       onEnd: function( options ) {
        $('div#$id').fadeOut('slow', function() { $('div#$id').remove(); });
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->start = $this->conf['start'];
    $this->end = $this->conf['end'];
    $this->target = $this->conf['target'];
    $this->html = $this->conf['html'];
    $this->class = isset($this->conf['class'])?$this->conf['class']:false;
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>