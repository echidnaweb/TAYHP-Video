<?php
/**
 * FlickrSet class
 * Represents a set of flickr photo results 
 *
 **/

class FlickrSet
{
  private $flickr_api;
  private $flickr_options = array(); 
  private $photos = array();
  private $options = array();

  function __construct($options)
  {
    $this->flickr_api = new phpFlickr(FLICKR_API_KEY);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);
    $this->flickr_api->enableLogLimiting(FLICKR_LOG_DIR,FLICKR_LOG_LIMIT);
    $this->options = $options; 
    $this->process();
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

  public function get_photos($quantity=10,$orientation=false)
  {
     $photos = array();
     for ($i = 0; $i < $quantity; $i++)
     {
       $photos[$i] = $this->get_random_photo($orientation);
       $photos[$i]['url'] = $this->flickr_api->buildPhotoURL($photos[$i], $this->options['size']); 
     }
     return $photos;
  }

  private function process()
  {
    if (!$this->photos = $this->flickr_api->photos_search($this->options))
      return false; 
    //print_r($this->options);
    //print_r($this->photos);
    return true;
  }
  
}


?>
