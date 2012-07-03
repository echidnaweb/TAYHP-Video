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
  private $start,$end,$target,$src,$height,$width;

  function __construct($conf)
  {
    $this->conf = $conf; 
    $this->preprocess();
    $this->process();
  }

  private function process()
  {
    if ($this->target = "%random%")
    {
      $this->js .= <<<EOF
      if (typeof target != 'undefined') var prevtarget = target;

      var target = $(".target").random(); 
      
      if (typeof prevtarget != 'undefined') 
      {
        while (target = prevtarget)
        {
          var target = $(".target").random();  
        { 
      }
EOF
    }

    $this->js .= <<<EOF
    
    var target = "$this->target"+Math.floor(Math.random()*5);
 
    pop.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         document.getElementById(target).innerHTML = "Yes";
       },
       onEnd: function( options ) {
         document.getElementById(target).innerHTML = "No";
       }
     });\n
EOF;
  }
  
  private function preprocess()
  {
    $oAPI = new TwitterAPI;
    $oAPI->processEvent($this->conf);
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = $this->conf['popcornOptions']['target'];
    $this->src = $this->conf['popcornOptions']['src'];
    $this->height = $this->conf['popcornOptions']['height'];
    $this->width = $this->conf['popcornOptions']['width'];
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
