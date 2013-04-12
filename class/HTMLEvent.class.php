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
    //concatenate html lines
    if (is_array($this->html))
    {
      $html = "";
      foreach ($this->html as $line)
      {
        $html .= $line;
      }
      $this->html = $html;
    }

    $this->html = addslashes($this->html);

    $id = uniqid("html_"); 
    $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
    $this->js .= <<<EOF
    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         var html = '<div id=\'$id\'>$this->html</div>';
         $classstatement
         $('#$this->target').html(html);
         $('#$this->target div#$id').fadeIn('slow');
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
