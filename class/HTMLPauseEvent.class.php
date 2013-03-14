<?php
/**
 * HTMLPauseEvent class
 * Writes arbitrary html to screen 
 * Generates javascript code from config array 
 *
 **/

class HTMLPauseEvent
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

    for($i = 0; $i < $this->occurrences; $i++)
    {
       $interval = $this->interval*1000;
       $delay = $this->delay*1000;
       $start = $delay+($interval*$i);
       $duration = $this->duration*1000;
       $end = $start+$duration;

       $id = uniqid("html_");
       if ($this->targets)
       {
         $target = array_shift($this->targets);
         array_push($this->targets,$target);
       } else
       {
         $target = $this->target;
       }


       $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
   
       $this->js .= <<<EOF

         pause_event_timer.push(setTimeout(function() {
           $classstatement
           var html = '<div id=\'$id\'>$this->html</div>';
           $('#$target').html(html);
           $('#$target div#$id').fadeIn('slow');
         }, $start));
         
         pause_event_timer.push(setTimeout(function() {
           $('div#$id').fadeOut('slow', function() { $('div#$id').remove(); });
         }, $end));

EOF;

     } 
  }

  private function preprocess()
  {
    $this->html = $this->conf['html'];
    $this->class = isset($this->conf['class'])?$this->conf['class']:false;
    $this->occurrences = isset($this->conf['occurrences'])?(int)$this->conf['occurrences']:1;
    $this->interval = isset($this->conf['interval'])?(int)$this->conf['interval']:5; 
    $this->targets = isset($this->conf['targets'])?$this->conf['targets']:false;
    $this->target = isset($this->conf['target'])?$this->conf['target']:false;
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
