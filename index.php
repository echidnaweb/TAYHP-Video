<!DOCTYPE html>
<html>
<head>
<title>The Are You Happy Project</title>
<style type="text/css">@import url("css/default.css");</style>
<style type="text/css">@import url("js/jcarousel/skins/tango/skin.css");</style>
<script src="js/popcorn-complete.js"></script>
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jcarousel/jquery.jcarousel.min.js"></script>
<script src="processtimeline.js.php?video=<?php echo isset($_GET['video'])?$_GET['video']:"31597454"; ?>"></script>
<script src="js/tayhpvideo.js"></script>
<script>document.addEventListener( "DOMContentLoaded",init_popcorn,false);</script>
</head>

<body>
  <div id="floater"></div>
  <div id="contentlayer"></div>
  <div id="videolayer">
    <div id="video"></div>
  </div> 
  <div id="attrib"></div>
  <div id="thumbdrawer">
    <div id="thumbtab">Choose another video</div>
    <div id="thumbs">
      <?php include('thumbs.php');?>
    </div>
  </div>
</body>
</html>
