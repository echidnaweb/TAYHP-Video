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
    
    $photos =  $this->get_random_photos($this->occurences,$this->orientations);

    for($i = 0; $i < count($photos); $i++)
    {
       //calculate start and end points for each repeat
       $start = $this->start+($i*$this->interval);
       $end =   $start+$this->duration;
       $url = $this->flickr_api->buildPhotoURL($photos[$i], $this->size);
       $alt = addslashes($photos[$i]['title']);
       $ownername = addslashes($photos[$i]['ownername']);
       $id = uniqid("flickr_");
       if ($this->targets)
       {
         $target = array_shift($this->targets);
         array_push($this->targets,$target);
       } 
       else $target = $this->target;

       $imgtag = "<img id=\"$id\" alt=\"$alt\" ".
              "src=\"" . $url . "\"></img>";

   $this->js .= <<<EOF
    
    //preload image
    $('<img/>')[0].src = '$url';;

    // Create a popcorn event
    popcorn.code({
       start: $start,
       end: $end,
       onStart: function( options ) {
         var imgtag = '$imgtag';
         $('body').attr('class','$this->template');
         $('#$target span#$this->id').fadeIn('slow');
         $('#$target').css('z-index',parseInt($('#$this->target').css('z-index'))+1);
         $('#$target').html(imgtag);
         $('#$target #$id').fadeIn('slow', function() { $('#$this->ownername_target').html('<strong>Photo courtesy of</strong>&nbsp;&nbsp;$ownername')});
       },
       onEnd: function( options ) {
        $('span#$this->id').fadeOut('slow', function() { $('span#$this->id').remove(); });
        $('#$this->ownername_target').empty();
       }
     });\n
EOF;

     }
  }

  private function preprocess()
  {
    $api_key = isset($this->conf['popcornOptions']['apikey'])?$this->conf['popcornOptions']['apikey']:FLICKR_API_KEY; 

    $this->id = isset($this->conf['id'])?$this->conf['id']:"0";
    $this->start = isset($this->conf['popcornOptions']['start'])?$this->conf['popcornOptions']['start']:"0";
    $this->end = isset($this->conf['popcornOptions']['end'])?$this->conf['popcornOptions']['end']:"0";
    $this->target = isset($this->conf['popcornOptions']['target'])?$this->conf['popcornOptions']['target']:"unknown";
    $this->ownername_target = isset($this->conf['popcornOptions']['ownername_target'])?$this->conf['popcornOptions']['ownername_target']:"unknown";
    $this->template = isset($this->conf['template'])?$this->conf['template']:"0"; 
    $this->orientation = isset($this->conf['popcornOptions']['orientation'])?$this->conf['popcornOptions']['orientation']:"landscape"; 
    $this->size = isset($this->conf['popcornOptions']['size'])?$this->conf['popcornOptions']['size']:false;
    $this->flickr_api = new phpFlickr($api_key);
    $this->flickr_api->enableCache("fs", CACHE_DIR,FLICKR_CACHE_EXPIRY);

    $this->occurences = isset($this->conf['occurences'])?(int)$this->conf['occurences']:1;
    $this->interval = isset($this->conf['interval'])?(int)$this->conf['interval']:5;
    $this->duration = isset($this->conf['duration'])?(int)$this->conf['duration']:5;
    $this->orientations = isset($this->conf['popcornOptions']['orientations'])?str_split($this->conf['popcornOptions']['orientations']):array($this->orientation[0]);
    $this->targets = isset($this->conf['popcornOptions']['targets'])?$this->conf['popcornOptions']['targets']:array($this->target);

    $this->conf['popcornOptions']['extras'] = "url_o,url_s,url_o,owner_name";
    $this->conf['popcornOptions']['per_page'] = "200";
    $this->conf['popcornOptions']['license'] = isset($this->conf['popcornOptions']['license'])?$this->conf['popcornOptions']['license']:"2,4";
   
    // set any orientation defaults for specific tag ids 
    if (!$this->orientation && isset(self::$orientation_defaults[$this->target]))
      $this->orientation = self::$orientation_defaults[$this->target];

    // set any size defaults for specific tag ids
    if (!$this->size && isset($this->size_defaults[$this->target]))
      $this->size = $this->size_defaults[$this->target];
    elseif (!$this->size)
      $this->size = "small";
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
