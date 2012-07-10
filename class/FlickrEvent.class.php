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

    // Pic x number of random photos from the results
    $randkeys = array_rand($this->photos['photo'],$this->numberofimages);
    $randkeys = is_array($randkeys)?$randkeys:array($randkeys);

    $imgtag = "";
    // Build img tags
    foreach ($randkeys as $randkey)
    {
      $photo =  $this->photos['photo'][$randkey];
      $photo_id = $this->id."_".$randkey;
      $imgtag .= "<span id='$this->id'><img alt='$photo[title]' id='$photo_id' ".
                 "src=" . $this->flickr_api->buildPhotoURL($photo, "Square") . "></span>";
    }

    $this->js .= <<<EOF
    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         var imgtag = "$imgtag";
         // If no template has been loaded yet or it has changed load template
         if (typeof window.template == 'undefined' && window.template != "$this->template")
         {
           window.template = "$this->template"; 
           
           $('#contentlayer').load('tpl/'+window.template+'.html', function() { $('#$this->target').html(imgtag); }); 
           $('#$this->target span#$this->id').fadeIn('slow');
         }
         else 
         {         
           $('#$this->target').html(imgtag);
           $('#$this->target span#$this->id').fadeIn('slow');
         } 
       },
       onEnd: function( options ) {
        //$('#$this->target img').fadeOut('slow', function() { $(window.eventlog['$this->id']).empty(); });
        $('span#$this->id').fadeOut('slow', function() { $('span#$this->id').remove(); });
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
    $this->numberofimages = $this->conf['numberofimages'];
    $this->flickr_options = $this->conf['popcornOptions']['flickr_options']; 

    $this->flickr_api = new phpFlickr($this->conf['popcornOptions']['flickr_options']['apikey']);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);

    //flickr.photos.search
    if (!$this->photos = $this->flickr_api->photos_search($this->conf['popcornOptions']['flickr_options']))
      return false; 

    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
}


?>
