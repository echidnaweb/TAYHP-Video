document.addEventListener( "DOMContentLoaded", function()
     $this->
{
  var popcorn = Popcorn.vimeo( "#video", "http://vimeo.com/29017015");
  
  /*
  popcorn.twitter({
  start:'1.0',
  end:'15.0',
  src:'#happy',
  target:'area1',
  height:'',
  width:''});
  */
  popcorn.subtitle({
  start: 1,
  end: 15,
  text: "ground control to major tom",
  target: "area2",
  });
}, false );
