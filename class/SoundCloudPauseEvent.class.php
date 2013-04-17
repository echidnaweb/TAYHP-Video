<?php
/**
 * SoundCloudPauseEvent class
 *
 **/

class SoundCloudPauseEvent
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
    for($i = 0; $i < $this->occurrences; $i++)
    {
       $interval = $this->interval*1000;
       $delay = $this->delay*1000;
       $start = $delay+($interval*$i);
       $duration = $this->duration*1000;
       $end = $start+$duration;

       $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
       $id = uniqid("soundcloud_"); 
       $this->js .= <<<EOF

         pause_event_timer.push(setTimeout(function() {
           $classstatement
           $('#audio').html('<iframe id="$id" width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F$this->trackid&amp;color=ff6600&amp;auto_play=true&amp;show_artwork=true"></iframe>');
         }, $start));
         
         pause_event_timer.push(setTimeout(function() {
           $('#$id').remove();
         }, $end));     

EOF;

     } 
  }

  private function preprocess()
  {
    $this->trackid = $this->conf['trackid'];
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
