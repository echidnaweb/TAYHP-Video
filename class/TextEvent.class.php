<?php
/**
 * TextEvent class
 * Writes arbitrary text to screen on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class TextEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end,$target,$height,$width,$text,$template,$class;

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
       end: $this->end,
       onStart: function( options ) {
         var text = '<span id=\'$this->id\' class=\'$this->class\'>$this->text</span>';
         $('body').attr('class','$this->template'); 
         $('#$this->target').html(text);
         $('#$this->target span#$this->id').fadeTo(1000, 0.999);
       },
       onEnd: function( options ) {
        $('span#$this->id').fadeOut('slow', function() { $('span#$this->id').remove(); });
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = $this->conf['popcornOptions']['target'];
    $this->text = $this->conf['popcornOptions']['text'];
    $this->template = isset($this->conf['template'])?$this->conf['template']:"";
    $this->class = isset($this->conf['class'])?$this->conf['class']:"";
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
