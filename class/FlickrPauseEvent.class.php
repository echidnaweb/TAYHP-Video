<?php
/**
 * FlickrPauseEvent class
 * Represents a single flickr event on the popcorn timeline
 * Generates javascript code from config array 
 *
 **/

class FlickrPauseEvent
{
  private $conf;
  private $js = "";
  private $flickr_api;
  private $flickr_options = array(); 
  private $photos = array();

  private static $size_defaults = array(
          'cwtch' => 'small',
          'flickrsolo' => 'medium');

  private static $orientation_defaults = array(
           'area1' => 'portrait',
           'area3' => 'landscape');

  function __construct($conf)
  {
    $this->conf = $conf; 
    if ($this->preprocess()) $this->process();
  }

  private function get_random_photos($quantity,$orientations)
  {
    $photos = array();
    $pool = $this->photos['photo'];
    shuffle($pool);
    
    // for each photo occurrence required 
    for ($i = 0; $i < $quantity; $i++)
    {
      $match = false;
      //shift the first value off the beginning of the string
      $orientation = array_shift($orientations);
      //and push it back onto the end
      array_push($orientations,$orientation);
     
      // loop through the flickr result pool 
      for ($j=0; $j < count($pool); $j++)
      {
        // ignore any results without dimension metadata
        if (!isset($pool[$j]['width_s']) || !isset($pool[$j]['width_s']))
         continue; 
        
        // return the first result with the required orientation 
        switch ($orientation) 
        {
          case 'p':
            if ($pool[$j]['height_s'] > $pool[$j]['width_s'])
            {
              // assign matching element to result array
              $photos[] = $pool[$j];
              // pull it out of the pool array and push it on the end
              array_push($pool,array_splice($pool,$j,1));
              $match = true;
              continue;
            }
            break;
          case 'l':
            if ($pool[$j]['width_s'] > $pool[$j]['height_s'])
            {
              // assign matching element to result array
              $photos[] = $pool[$j];
              // pull it out of the pool array and push it on the end
              array_push($pool,array_splice($pool,$j,1));
              $match = true;
              continue;
            }
            break;
        }
        
        if ($match) break;
      }
     
      // if we still don't have a match just pick the first element 
      if (!$match)
      {
         $photos[] = $pool[0];
         array_push($pool,array_splice($pool,0,1));
      }
       
    }
    return $photos;
  }

  private function process()
  {
    if (!$this->photos || $this->photos['total'] == 0) return false;
    
    $photos =  $this->get_random_photos($this->occurrences,$this->orientations);

    for($i = 0; $i < count($photos); $i++)
    {
       $interval = $this->interval*1000;
       $delay = $this->delay*1000;
       $start = $delay+($interval*$i);
       $duration = $this->duration*1000;
       $end = $start+$duration;
       $url = $this->flickr_api->buildPhotoURL($photos[$i], $this->size);
       $alt = addslashes($photos[$i]['title']);
       $ownername = addslashes($photos[$i]['ownername']);

       $id = uniqid("flickr_");
       if ($this->targets)
       {
         $target = array_shift($this->targets);
         array_push($this->targets,$target);
       } else
       {
         $target = $this->target;
       }

       $imgtag = "<img id=\"$id\" alt=\"$alt\" ".
              "src=\"" . $url . "\"></img>";
       $classstatement = $this->class?"$('body').attr('class','$this->class');":"";
   $this->js .= <<<EOF
         
         //preload image
         $('<img/>')[0].src = '$url';;

         pause_event_timer.push(setTimeout(function() {
           $classstatement
           var imgtag = '$imgtag';
           $('#$target').css('z-index',parseInt($('#$this->target').css('z-index'))+1);
           $('#$target').html(imgtag);
           $('#$target #$id').fadeIn('slow', function() { $('#$this->ownername_target').html('<strong>Photo courtesy of</strong>&nbsp;&nbsp;$ownername')}); 
         }, $start));

         setTimeout(function() {
           $('#$id').fadeOut('slow', function() { $('#$id').remove(); });
           $('#$this->ownername_target').empty();
         }, $end);

EOF;
     }
  }

  private function preprocess()
  {
    $api_key = isset($this->conf['apikey'])?$this->conf['apikey']:FLICKR_API_KEY;
    $this->flickr_api = new phpFlickr($api_key);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);
    $this->class = isset($this->conf['class'])?$this->conf['class']:false;
    $this->occurrences = isset($this->conf['occurrences'])?(int)$this->conf['occurrences']:1;
    $this->interval = isset($this->conf['interval'])?(int)$this->conf['interval']:5;
    $this->duration = isset($this->conf['duration'])?$this->conf['duration']:5;
    $this->target = isset($this->conf['target'])?$this->conf['target']:false;
    $this->targets = isset($this->conf['targets'])?$this->conf['targets']:array($this->target);
    $this->delay = isset($this->conf['delay'])?$this->conf['delay']:0;
    $this->ownername_target = isset($this->conf['ownername_target'])?$this->conf['ownername_target']:"unknown";
    $this->orientation = isset($this->conf['orientation'])?$this->conf['orientation']:"landscape";
    $this->orientations = isset($this->conf['orientations'])?str_split($this->conf['orientations']):array($this->orientation[0]);
    $this->size = isset($this->conf['size'])?$this->conf['size']:false;

    $this->conf['extras'] = "url_o,url_s,url_o,owner_name";
    $this->conf['per_page'] = "200";
    $this->conf['license'] = isset($this->conf['license'])?$this->conf['license']:"2,4";    

    // set any orientation defaults for specific tag ids
    if (!$this->orientation && isset(self::$orientation_defaults[$this->target]))
      $this->orientation = self::$orientation_defaults[$this->target];

    // set any size defaults for specific tag ids
    if (!$this->size && isset($this->size_defaults[$this->target]))
      $this->size = $this->size_defaults[$this->target];
    elseif (!$this->size)
      $this->size = "small";
    if (!$this->photos = $this->flickr_api->photos_search($this->removeArrayVals($this->conf)))
      return false;

    return true;
  }

  public function getJS()
  {
    return $this->js;
  }
  
  private function removeArrayVals($conf)
  {
    foreach ($conf as $key => $val)
    {
      if (is_array($val)) unset($conf[$key]);
    }
    return $conf;
  }
}


?>
