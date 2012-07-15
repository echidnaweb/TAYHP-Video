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
  private $id,$start,$end,$target,$text,$template,$class,$style;

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

         var text = '<span id=\'$this->id\' style=\'$this->style\' class=\'$this->class\'>$this->text</span>';

         // If no template has been loaded yet or it has changed load template
         if (typeof window.template == 'undefined' || window.template != "$this->template")
         {
           window.template = "$this->template";
           $('#contentlayer').load('tpl/'+window.template+'.html', function()
           {
             // get the left and width values of the target tag
             var leftpx = parseInt($('#$this->target').css('left'));
             var widthpx = parseInt($('#$this->target').css('width'));

             // set the class of the body tag to the template name
             $('body').attr('class',window.template);

             // move the target tag offscreen to the left 
             $('#$this->target').css('left','-'+widthpx+"px");

             // write the text to the target tag
             $('#$this->target').html(text);
             
             // un-hide the dynamic content
             $('#$this->target #$this->id').show();
             
             //animate the target tag 
             $('#$this->target').animate({"left": "+="+widthpx*2+"px"}, 19200);
           });
         }
         else
         {
           // get the left and width values of the target tag
           var leftpx = parseInt($('#$this->target').css('left'));
           var widthpx = parseInt($('#$this->target').css('width'));

           // move the target tag offscreen to the left 
           $('#$this->target').css('left','-'+widthpx+"px");

           // write the text to the target tag
           $('#$this->target').html(text);
           
           // un-hide the dynamic content
           $('#$this->target #$this->id').show();

           //animate the target tag
           $('#$this->target').animate({"left": "+="+widthpx*2+"px"}, 19200);
         }

       },
       onEnd: function( options ) {
        $('#$this->target').css('left','-'+widthpx+"px");
        $('#$this->target #$this->id').remove();
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
    $this->target = $this->conf['popcornOptions']['target'];
    $this->text = addslashes(preg_replace("/[^a-zA-Z0-9 !@#\$%\^\*\?\.;&:\-\+=\/]/","",nl2br($this->conf['popcornOptions']['text'])));
    $this->template = $this->conf['template'];
    $this->class = isset($this->conf['class'])?$this->conf['class']:"";
    $this->style = isset($this->conf['style'])?$this->conf['style']:"";
    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
