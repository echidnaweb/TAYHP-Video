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
        $("#audio").css('display','inline');
    //if closed then open
    }else if (bottomval == "-120px") {
        $("#thumbdrawer").animate({"bottom": "+=120px"}, "slow");
        $("#audio").css('display','none');
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

function set_compat(val)
{
  window.compat = val;

  if (val > 0)
  {
    $('#compat').html("<a href=\"javascript:set_compat(0)\">switch to full version</a>");
  }
  else
  {
    $('#compat').html("<a href=\"javascript:set_compat(1)\">not working? - try compatibility mode</a>");
  }

  if ($("#video iframe").length) load_video(window.curvideo); 
}

function load_video(id)
{
  if ($('#compat').html() == "") set_compat(0);

  window.curvideo = id;

  $('[id^=fd-]').css('display','none');
  $("#fd-"+id).css('display','inline');
  $("#video").html("<img style=\"display: block; margin: 200px auto\" src=\"http://theareyouhappyproject.org/wp-content/themes/starkers/images/intro-assets/ajax-loader.gif\" alt=\"video loading\" width=\"32\" height=\"32\" />");
  if (typeof intro_event_timer != "undefined")
  {
    for (var i = 0; i < intro_event_timer.length; i++)
    {
      clearTimeout(intro_event_timer[i]);
    }
  }

  //these lines causing 'Uncaught Error: NotFoundError: DOM Exception 8' error in Chrome with video-specific url
  if ($('#container2').length) { $('#container2').remove(); }
  if ($('#audio *').length) { $('#audio *').remove(); }
  if ($('.target').length) { $('.target').empty(); }

  close_drawer();

  if ((window.compat < 1)||(typeof window.compat === 'undefined'))
  {
    $.getScript("videos/processtimeline.js.php?video="+id+"&ts="+new Date().getTime())
      .done(function(script, textStatus) {
        init_popcorn();
      })
      .fail(function(jqxhr, settings, exception) {
        //alert(exception);
      });
  }
  else { load_plain_video(id); }
}

function load_plain_video(id)
{
  var vid_id = {
    argentina: 0,
    jharkhand: 0, 
    maharashtra: 0,
    mongolia: 0,
    peru: 0,
    tasmania: 69243208,
    uganda: 0,
    wales: 0 
  };

  //alert('launching plain '+vid_id[id]);
  
  $('#video').html('<iframe src="http://player.vimeo.com/video/'+vid_id[id]+'?autoplay=1" width="800" height="400" frameborder="0"></iframe>');
  $('[id^=fd-]').delay(2400).fadeOut(800); 

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
