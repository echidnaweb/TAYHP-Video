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
  private $id,$start,$end,$target,$ownername_target,$template,$orientation;
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
    $alt = addslashes($photo['title']);
    $ownername = addslashes($photo['ownername']);
    $imgtag = "<img id=\"$this->id\" alt=\"$alt\" ".
              "src=\"" . $url . "\"></img>";

    $this->js .= <<<EOF

    //preload image
    $('<img/>')[0].src = '$url';;

    // Create a popcorn event 
    popcorn.code({
       start: $this->start,
       end: $this->end,
       onStart: function( options ) {
         var imgtag = '$imgtag';
         $('body').attr('class','$this->template');
         $('#$this->target').css('z-index',parseInt($('#$this->target').css('z-index'))+1);
         $('#$this->target').html(imgtag);
         $('#$this->target #$this->id').fadeIn('slow', function() { $('#$this->ownername_target').html('<strong>Photo courtesy of</strong>&nbsp;&nbsp;$ownername')});
       },
       onEnd: function( options ) {
        $('#$this->id').fadeOut('slow', function() { $('#$this->id').remove(); });
        $('#$this->ownername_target').empty();
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
    $this->ownername_target = isset($this->conf['popcornOptions']['ownername_target'])?$this->conf['popcornOptions']['ownername_target']:"unknown";
    $this->template = isset($this->conf['template'])?$this->conf['template']:"0"; 
    $this->orientation = isset($this->conf['popcornOptions']['orientation'])?$this->conf['popcornOptions']['orientation']:false; 
    $this->size = isset($this->conf['small'])?$this->conf['size']:"small";

    $this->flickr_api = new phpFlickr($this->conf['popcornOptions']['apikey']);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);

    $this->conf['popcornOptions']['extras'] = "url_o,url_s,url_o,owner_name";
    
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
