<?php
/**
 * TwitterPauseEvent class
 * Represents a single twitter event on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class TwitterPauseEvent
{
  private $conf;
  private $js = "";
  private $tweets = array();

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    for($i = 0; $i < count($this->tweets); $i++)
    {
       $from_user = $this->tweets[$i]['from_user'];
       $text = addslashes(preg_replace("/[^a-zA-Z0-9 !@#\$%\^\*\?\.;&:\-\+=\/]/","",nl2br($this->tweets[$i]['text'])));
       $interval = $this->interval*1000;
       $delay = $this->delay*1000;
       $start = $delay+($interval*$i);
       $duration = $this->duration*1000;
       $end = $start+$duration;

       $id = uniqid("tweet_");
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
           var tweet_text = '<span id=\'$id\' class=\'$this->class\'>@'+'$from_user<br>$text</span>';
           $('#$target').html(tweet_text);
           $('#$target span#$id').fadeIn('slow');
         }, $start));
         
         pause_event_timer.push(setTimeout(function() {
           $('span#$id').fadeOut('slow', function() { $('span#$id').remove(); });
         }, $end));

EOF;

     } 
  }

  private function preprocess()
  {
    $oAPI = new TwitterAPI;
    $oAPI->processEvent($this->conf,$this->tweets);
    if (!count($this->tweets)) return false;

    $this->src = $this->conf['src'];
    $this->class = isset($this->conf['class'])?$this->conf['class']:"";
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
