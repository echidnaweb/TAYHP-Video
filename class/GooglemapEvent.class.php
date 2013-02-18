<?
/**
 * GooglemapEvent class
 * Event to display google map in popcorn production 
 * Generates javascript code from config array 
 *
 **/

class GooglemapEvent
{
  private $conf;
  private $js = "";

  function __construct($conf)
  {
    $this->conf = $conf; 
    $this->process();
  }

  private function process()
  {
    $options = json_encode($this->conf);
    $options = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $options); 
   
    $this->js .= <<<EOF
    popcorn.googlemap($options);

EOF;

 }
  
  public function getJS()
  {
    return $this->js;
  }
}


?>
