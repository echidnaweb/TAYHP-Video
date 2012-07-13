document.addEventListener( "DOMContentLoaded",function() {

  $('#video').empty();
  jQuery('#video-carousel').jcarousel({
    wrap: 'circular'
  });

  $("#thumbtab").click(toggle_drawer);

},false);

function toggle_drawer()
{
    var bottomval = $('#thumbdrawer').css('bottom');
    if(bottomval == "0px"){
        $("#thumbdrawer").animate({"bottom": "-=100px"}, "slow");
    }else if (bottomval == "-100px") {
        $("#thumbdrawer").animate({"bottom": "+=100px"}, "slow");
    }
    else { }
}

function load_video(id)
{
  toggle_drawer();
  $.getScript("processtimeline.js.php?video="+id+"&ts=<?php echo time(); ?>")
.done(function(script, textStatus) {
  init_popcorn();
})
.fail(function(jqxhr, settings, exception) {
  alert(exception);
});
}
