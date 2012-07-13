<!DOCTYPE html>
<html>
<head>
<title>The Are You Happy Project</title>
<style type="text/css">@import url("css/default.css");</style>
<style type="text/css">@import url("js/jcarousel/skins/tango/skin.css");</style>
<script src="js/popcorn-complete.js"></script>
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jcarousel/jquery.jcarousel.min.js"></script>
<script src="processtimeline.js.php?video=flickr"></script>
<script>
document.addEventListener( "DOMContentLoaded",init_popcorn,false);

function load_video(id)
{
  $.getScript("processtimeline.js.php?video="+id+"&ts=<?php echo time(); ?>")
.done(function(script, textStatus) {
  init_popcorn();
  //console.log( textStatus );
})
.fail(function(jqxhr, settings, exception) {
  alert(exception);
  //$( "div.log" ).text( "Triggered ajaxError handler." );
}); 
}

</script>
</head>

<body>
  <div id="floater"></div>
  <div id="contentlayer"></div>
  <div id="videolayer">
    <div id="video"></div>
  </div> 
  <div id="thumbdrawer">
    <div id="thumbtab">Choose another video</div>
    <div id="thumbs">
      <?php include('thumbs.php');?>
    </div>
  </div>
</body>
</html>
