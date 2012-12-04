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
  $('.target').empty();
  toggle_drawer();
  $.getScript("processtimeline.js.php?video="+id+"&ts=<?php echo time(); ?>")
.done(function(script, textStatus) {
  init_popcorn();
})
.fail(function(jqxhr, settings, exception) {
  //alert(exception);
});
}

// Helper function for sending a message to the player
function playercmd(action, value) {
  var data = { method: action };

  var f = $('iframe'),
  url = f.attr('src').split('?')[0]

  if (value) {
      data.value = value;
  }

  f[0].contentWindow.postMessage(JSON.stringify(data), url);
}
