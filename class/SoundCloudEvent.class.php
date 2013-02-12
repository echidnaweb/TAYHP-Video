<?php
/**
 * SoundCloudEvent class
 * Event to play a SoundCloud track during a production 
 * Generates javascript code from config array 
 *
 **/

class SoundCloudEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end,$trackid;

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    $id = uniqid("soundcloud_");
    $this->js .= <<<EOF
    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         $('#audio').html('<iframe id="$id" width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F71861721&amp;color=ff6600&amp;auto_play=true&amp;show_artwork=true"></iframe>');
       },
       onEnd: function( options ) {
         $('#$id').remove();
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $this->start = isset($this->conf['popcornOptions']['start'])?$this->conf['popcornOptions']['start']:"0";
    $this->end = isset($this->conf['popcornOptions']['end'])?$this->conf['popcornOptions']['end']:"0";
    $this->trackid = isset($this->conf['trackid'])?$this->conf['trackid']:"0";
    return true;
  }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
