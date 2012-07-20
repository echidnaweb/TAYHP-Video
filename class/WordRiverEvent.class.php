<?php
/**
 * WordRiverEvent class
 * Animates text accross the screen 
 * Generates javascript code from config array 
 *
 **/

class WordRiverEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end,$target,$text,$template,$class,$style,$origin;

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {
    switch ($this->origin)
    {
       case "left":
       case "right":
         $ststmt = "var start_point = '-'+$('#$this->target').width()+'px';";
         $dtstmt = "var distance = $('#$this->target').width()+$(window).width()+'px';";
       break;

       case "top":
       case "bottom":
         $ststmt = "var start_point = '-'+$('#$this->target').height()+'px';";
         $dtstmt = "var distance = $('#$this->target').height()+$(window).height()+'px';";
       break;
    }
    $this->js .= <<<EOF
    // Create a popcorn event
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {

         var text = '<span id=\'$this->id\' style=\'$this->style\' class=\'$this->class\'>$this->text</span>';
 
         //set position of target back to default
         $('#$this->target').css('left', '');
         $('#$this->target').css('right', '');
         $('#$this->target').css('top', '');
         $('#$this->target').css('bottom', '');
    
         // set the class of the body tag to the template name
         $('body').attr('class','$this->template');

         $ststmt
         $dtstmt

         // move the target tag offscreen 
         $('#$this->target').css('$this->origin',start_point);

         // write the text to the target tag
         $('#$this->target').html(text);
             
         // un-hide the dynamic content
         $('#$this->target #$this->id').show();
             
         //animate the target tag 
         $('#$this->target').animate({"$this->origin": "+="+distance}, 19200,'linear',function() { 
           $('#$this->target').css('$this->origin',start_point); $('#$this->id').remove();
         });
       },
       onEnd: function( options ) {
       }
     });\n

EOF;

  }  
  
  private function preprocess()
  {
    $oAPI = new TwitterAPI;
    $oAPI->processEvent($this->conf);

    if (!isset($this->conf['popcornOptions']['text'])) return false;

    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = isset($this->conf['popcornOptions']['target'])?$this->conf['popcornOptions']['target']:"unknown";
    $this->text = addslashes($this->conf['popcornOptions']['text']);
    $this->template = isset($this->conf['template'])?$this->conf['template']:"";
    $this->class = isset($this->conf['class'])?$this->conf['class']:"";
    $this->style = isset($this->conf['style'])?$this->conf['style']:"";
    $this->origin = isset($this->conf['popcornOptions']['origin'])?$this->conf['popcornOptions']['origin']:"right";
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
