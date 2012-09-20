<?

// shows the list of videos and paths to their thumbnails
$thumbs  = array(
25726263 => "http://b.vimeocdn.com/ts/334/657/334657093_100.jpg", // UGANDA (wrong thumbnail for now, but can't locate the Uganda one)
45636689 => "http://b.vimeocdn.com/ts/317/451/317451255_100.jpg", // PERU
44798169 => "http://b.vimeocdn.com/ts/311/289/311289758_100.jpg", // TASMANIA
33500169 => "http://b.vimeocdn.com/ts/227/414/227414986_100.jpg", // TRINIDAD
47748959 => "http://b.vimeocdn.com/ts/218/803/218803459_100.jpg", // ARGENTINA - this needs editing before it will work
26646996 => "http://b.vimeocdn.com/ts/215/148/215148023_100.jpg", // MONGOLIA
31595710 => "http://b.vimeocdn.com/ts/213/089/213089500_100.jpg", // BRISTOL
31597454 => "http://b.vimeocdn.com/ts/213/102/213102268_100.jpg", // MAHARASHTRA
29045451 => "http://b.vimeocdn.com/ts/194/235/194235512_100.jpg", // ARGENTINA 2
29017015 => "http://b.vimeocdn.com/ts/194/033/194033329_100.jpg", // ARGENTINA 1 (ORIG?)
28764822 => "http://b.vimeocdn.com/ts/192/166/192166923_100.jpg", // GLASGOW
28508255 => "http://b.vimeocdn.com/ts/190/274/190274883_100.jpg", // BULGARIA (COMMENTED OUT FOR SOME REASON?)
26646996 => "http://b.vimeocdn.com/ts/176/188/176188947_100.jpg" 	// ARGENTINA 2 (ORIG?)
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
