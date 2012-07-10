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
  private $id,$start,$end,$target,$src,$height,$width,$text,$from_user,$template;

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
         var tweet_text = '<span id=\'$this->id\'>@'+'$this->from_user<br>$this->text</span>';
         // If no template has been loaded yet or it has changed load template
         if (typeof window.template == 'undefined' && window.template != "$this->template")
         {
           window.template = "$this->template"; 
           $('#contentlayer').load('tpl/'+window.template+'.html', function() { $('#$this->target').html(tweet_text); }); 
           $('#$this->target span#$this->id').fadeIn('slow');
         }
         else
         {
           $('#$this->target').html(tweet_text);
           $('#$this->target span#$this->id').fadeIn('slow');
         }
       },
       onEnd: function( options ) {
        $('span#$this->id').fadeOut('slow', function() { $('span#$this->id').remove(); });
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $oAPI = new TwitterAPI;
    $oAPI->processEvent($this->conf);

    if (!isset($this->conf['popcornOptions']['tweet'])) return false;

    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = $this->conf['popcornOptions']['target'];
    $this->src = $this->conf['popcornOptions']['src'];
    $this->from_user = $this->conf['popcornOptions']['tweet']['from_user'];
    $this->text = addslashes(preg_replace("/[^a-zA-Z0-9 !@#\$%\^\*\?\.;&:\-\+=\/]/","",nl2br($this->conf['popcornOptions']['tweet']['text'])));
    $this->height = $this->conf['popcornOptions']['height'];
    $this->width = $this->conf['popcornOptions']['width'];
    $this->template = $this->conf['template'];
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
