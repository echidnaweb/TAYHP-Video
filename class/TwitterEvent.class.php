<?php
/**
 * TwitterEvent class
 * Represents a single twitter event on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class TwitterEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end,$target,$src,$height,$width,$text,$from_user,$template,$class;
  private $tweets = array();

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    for($i = 0; $i < $this->occurences; $i++)
    {
       //calculate start and end points for each repeat
       $start = $this->start+($i*$this->interval);
       $end =   $start+$this->duration;
       $from_user = $this->tweets[$i]['from_user'];
       $text = addslashes(preg_replace("/[^a-zA-Z0-9 !@#\$%\^\*\?\.;&:\-\+=\/]/","",nl2br($this->tweets[$i]['text']))); 
       $id = uniqid("tweet_");
       if ($this->targets) 
       { 
         $target = array_shift($this->targets);
         array_push($this->targets,$target);
       } else $target = $this->target;
       
   $this->js .= <<<EOF
    // Create a popcorn event
    popcorn.code({
       start: $start,
       end: $end,
       onStart: function( options ) {
         var tweet_text = '<span id=\'$id\' class=\'$this->class\'>@'+'$from_user<br>$text</span>';
         $('body').attr('class','$this->template');
         $('#$target').html(tweet_text);
         $('#$target span#$id').fadeIn('slow');
       },
       onEnd: function( options ) {
        $('span#$id').fadeOut('slow', function() { $('span#$this->id').remove(); });
       }
     });\n
EOF;

     } 
  }

  private function preprocess()
  {
    $oAPI = new TwitterAPI;
    $oAPI->processEvent($this->conf,$this->tweets);

    if (!count($this->tweets)) return false;

    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = $this->conf['popcornOptions']['target'];
    $this->src = $this->conf['popcornOptions']['src'];
    $this->template = isset($this->conf['template'])?$this->conf['template']:"";
    $this->class = isset($this->conf['class'])?$this->conf['class']:"";
    $this->occurences = isset($this->conf['occurences'])?(int)$this->conf['occurences']:1;
    $this->interval = isset($this->conf['interval'])?(int)$this->conf['interval']:5; 
    $this->duration = isset($this->conf['duration'])?(int)$this->conf['duration']:$this->end-$this->start;
    $this->targets = isset($this->conf['targets'])?$this->conf['targets']:false;
    $this->target = isset($this->conf['popcornOptions']['target'])?$this->conf['popcornOptions']['target']:false;
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }

}


?>
