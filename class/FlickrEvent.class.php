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
  private $id,$start,$end,$target,$template,$orientation;
  private $flickr_api;
  private $flickr_options = array(); 
  private $photos = array();

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function get_random_photo($orientation=false)
  {
    $photos = $this->photos['photo'];
    shuffle($photos);
 
    foreach ($photos as $photo)
    {
      if (!isset($photo['width_s']) || !isset($photo['width_s']))
       continue; 

      switch ($orientation) 
      {
        case "landscape":
          if ($photo['width_s'] > $photo['height_s']) return $photo;
          break;
        case "portrait":
          if ($photo['height_s'] > $photo['width_s']) return $photo;
          break;
        default:
          return $photo;
      }
    }

    // if a photo of the requested orientation isn't available just return the first one 
    return $photos[0];
  }

  private function process()
  {
    if (!$this->photos || count($this->photos) < 1) return false;

    $photo =  $this->get_random_photo($this->orientation);
    $url = $this->flickr_api->buildPhotoURL($photo, $this->size);
    $imgtag = "<img id='$this->id' alt='$photo[title]'".
              "src=" . $url . "></img>";

    $this->js .= <<<EOF

    //preload image
    $('<img/>')[0].src = '$url';;

    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         var imgtag = "$imgtag";
         // If no template has been loaded yet or it has changed load template
         if (typeof window.template == 'undefined' || window.template != "$this->template")
         {
           window.template = "$this->template"; 
           $('#contentlayer').load('tpl/'+window.template+'.html', function() 
           {
             $('body').attr('class',window.template);
             $('#$this->target').html(imgtag);
             $('#$this->target #$this->id').fadeIn('slow');
           }); 
         }
         else 
         {         
           $('#$this->target').html(imgtag);
           $('#$this->target #$this->id').fadeIn('slow');
         } 
       },
       onEnd: function( options ) {
        //$('#$this->target img').fadeOut('slow', function() { $(window.eventlog['$this->id']).empty(); });
        $('#$this->id').fadeOut('slow', function() { $('#$this->id').remove(); });
       }
     });\n

EOF;

  }
  
  private function preprocess()
  {
    if (!isset($this->conf['popcornOptions']['apikey'])) return false; 

    $this->id = isset($this->conf['id'])?$this->conf['id']:"0";
    $this->start = isset($this->conf['popcornOptions']['start'])?$this->conf['popcornOptions']['start']:"0";
    $this->end = isset($this->conf['popcornOptions']['end'])?$this->conf['popcornOptions']['end']:"0";
    $this->target = isset($this->conf['popcornOptions']['target'])?$this->conf['popcornOptions']['target']:"unknown";
    $this->template = isset($this->conf['template'])?$this->conf['template']:"0"; 
    $this->orientation = isset($this->conf['popcornOptions']['orientation'])?$this->conf['popcornOptions']['orientation']:false; 
    $this->size = isset($this->conf['small'])?$this->conf['size']:"small";

    $this->flickr_api = new phpFlickr($this->conf['popcornOptions']['apikey']);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);

    $this->conf['popcornOptions']['extras'] = "url_o,url_s,url_o";
    
    if (!$this->photos = $this->flickr_api->photos_search($this->conf['popcornOptions']))
      return false; 

    return true;
  }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
