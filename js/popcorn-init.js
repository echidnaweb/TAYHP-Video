document.addEventListener( "DOMContentLoaded", function()
{
  var popcorn = Popcorn.vimeo( "#video", "http://vimeo.com/29017015");

  popcorn.twitter({
  start:'1.0',
  end:'15.0',
  src:'#happy',
  target:'area1',
  height:'',
  width:''});

}, false );
