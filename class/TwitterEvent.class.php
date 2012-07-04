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
  private $id,$start,$end,$target,$src,$height,$width,$template;

  function __construct($conf)
  {
    $this->conf = $conf; 
    $this->preprocess();
    $this->process();
  }

  private function process()
  {
    $this->js .= <<<EOF
    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
        // If no template has been loaded yet or it has changed load template
        if (typeof window.template == 'undefined' && window.template != "$this->template")
        {
          window.template = "$this->template"; 
          $('#contentlayer').load('tpl/'+template+'.html'); 
        }

EOF;

        // If a random target is required, pick one
        if ($this->target == '%random%')
          $this->js .= "        target = $('.target').random();\n"; 
        else 
          $this->js .= "        target = $('$this->target');\n"; 

   $this->js .= <<<EOF
        //alert('writing to: '+target.attr('id'));
        target.text('$this->text');
        window.eventlog['$this->id'] = target.attr('id');
       },
       onEnd: function( options ) {
        //alert ('emptying: '+window.eventlog['$this->id']);
        $(window.eventlog['$this->id']).text = ''; 
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    $oAPI = new TwitterAPI;
    $oAPI->processEvent($this->conf);
    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = $this->conf['popcornOptions']['target'];
    $this->src = $this->conf['popcornOptions']['src'];
    $this->from_user = $this->conf['popcornOptions']['tweet']['from_user'];
    $this->text = addslashes(preg_replace("/[\n\r]/","",$this->conf['popcornOptions']['tweet']['text']));
    $this->height = $this->conf['popcornOptions']['height'];
    $this->width = $this->conf['popcornOptions']['width'];
    $this->template = $this->conf['template'];
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
