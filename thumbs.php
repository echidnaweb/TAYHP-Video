<?

// shows the list of videos and paths to their thumbnails
$thumbs  = array(
uganda => "http://b.vimeocdn.com/ts/420/738/420738487_100.jpg",      // UGANDA
peru   => "http://b.vimeocdn.com/ts/420/717/420717525_100.jpg",			 // PERU
wales  => "http://b.vimeocdn.com/ts/420/752/420752810_100.jpg",      // WALES
maharashtra => "http://b.vimeocdn.com/ts/424/366/424366603_100.jpg", // MAHARASHTRA
jharkhand => "http://b.vimeocdn.com/ts/420/264/420264171_100.jpg",   // JHARKHAND
argentina => "http://b.vimeocdn.com/ts/420/778/420778755_100.jpg",   // ARGENTINA
mongolia => "http://b.vimeocdn.com/ts/420/945/420945390_100.jpg",    // MONGOLIA 
tasmania => "http://b.vimeocdn.com/ts/424/507/424507443_100.jpg"     // TASMANIA
);
$num_thumbs = count($thumbs);

?>
<ul id="video-carousel" class="jcarousel-skin-tango">
<?php 
$index = 1;
foreach  ($thumbs as $id => $thumb_url)
{
  echo "<li class=\"jcarousel-item-$index\">\n";
  echo "<a href=\"javascript:load_video('$id')\">\n";
  echo "<img src=\"$thumb_url\"></img></a>\n";
  echo "</li>\n";
}
?>
</ul>
