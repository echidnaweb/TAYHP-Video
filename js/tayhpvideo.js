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
    //if open then close
    if(bottomval == "0px"){
        $("#thumbdrawer").animate({"bottom": "-=120px"}, "slow");
    //if closed then open
    }else if (bottomval == "-120px") {
        $("#thumbdrawer").animate({"bottom": "+=120px"}, "slow");
    }
    else { }
}

function open_drawer()
{
  if ($('#thumbdrawer').css('bottom') == "-120px") toggle_drawer(); 
}


function close_drawer()
{
  if ($('#thumbdrawer').css('bottom') == "0px") toggle_drawer(); 
}

function load_video(id)
{
  $("#video").html("<img style=\"display: block; margin: 200px auto\" src=\"http://theareyouhappyproject.org/wp-content/themes/starkers/images/intro-assets/ajax-loader.gif\" alt=\"video loading\" width=\"32\" height=\"32\" />");
  if (typeof intro_event_timer != "undefined")
  {
    for (var i = 0; i < intro_event_timer.length; i++)
    {
      clearTimeout(intro_event_timer[i]);
    }
  }

  $('#container2').remove();
  $('#audio *').remove();
  $('.target').empty();
  toggle_drawer();
  //alert("processtimeline.js.php?video="+id+"&ts="+new Date().getTime());
  $.getScript("videos/processtimeline.js.php?video="+id+"&ts="+new Date().getTime())
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

  var f = $('iframe');
  var url = f.attr('src').split('?')[0]

  if (value) {
      data.value = value;
  }

  f[0].contentWindow.postMessage(JSON.stringify(data), url);
}
