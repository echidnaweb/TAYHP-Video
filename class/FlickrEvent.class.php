<?php
/**
 * FlickrEvent class
 * Represents a single flickr event on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class FlickrEvent
{
  private $conf;
  private $js = "";
  private $id,$start,$end,$target,$template,$numberofimages;
  private $flickr_api;
  private $flickr_options = array(); 
  private $photos = array();

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function process()
  {

    if (!$this->photos || count($this->photos) < 1) return false;
 
    $imgtag = "";
    $randkeys = array_rand($this->photos,$this->numberofimages);
    foreach ($randkeys as $randkey)
    {
      $photo =  $this->photos[$randkey];
      
      $imgtag .= "<img border='0' alt='$photo[title]' ".
                 "src=" . $this->flickr_api->buildPhotoURL($photo, "Square") . ">";
    }

    $this->js .= <<<EOF
    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         var imgtag = '$imgtag';
         // If no template has been loaded yet or it has changed load template
         if (typeof window.template == 'undefined' && window.template != "$this->template")
         {
           window.template = "$this->template"; 
           
           
           $('#contentlayer').load('tpl/'+window.template+'.html', function() { $('#$this->target').html(imgtag); }); 
         }
         else $('#$this->target').html(imgtag);
         window.eventlog['$this->id'] = '#$this->target';
       },
       onEnd: function( options ) {
        $(window.eventlog['$this->id']).empty(); 
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    if (!isset($this->conf['popcornOptions']['flickr_options']['apikey'])) return false; 

    $this->id = $this->conf['id'];
    $this->start = $this->conf['popcornOptions']['start'];
    $this->end = $this->conf['popcornOptions']['end'];
    $this->target = $this->conf['popcornOptions']['target'];
    $this->template = $this->conf['template'];
    $this->flickr_options = $this->conf['popcornOptions']['flickr_options']; 

    if (!isset($this->conf['popcornOptions']['flickr_options']['apikey']))
      return false;

    $this->flickr_api = new phpFlickr($this->conf['popcornOptions']['flickr_options']['apikey']);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);
    
    //flickr.photos.search
    if ($this->photos = $this->flickr_api->photos_search($this->conf['popcornOptions']['flickr_options']))
      return false; 

    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
