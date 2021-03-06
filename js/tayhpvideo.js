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
    $('#compat').html("<a href=\"javascript:set_compat(0)\">Switch to realtime version</a>");
  }
  else
  {
    $('#compat').html("<a href=\"javascript:set_compat(1)\" title=\"Watch a version we captured earlier - for slower internet connections and IE users\">Not working?</a>");
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
    //alert(intro_event_timer);
    for (var i = 0; i < intro_event_timer.length; i++)
    {
      //alert("-->"+intro_event_timer[i]);
      clearTimeout(intro_event_timer[i]);
    }
  }

  if (typeof pause_event_timer != "undefined")
  {
    //alert(pause_event_timer);
    for (var i = 0; i < pause_event_timer.length; i++)
    {
      clearTimeout(pause_event_timer[i]);
    }
  }

  //these lines causing 'Uncaught Error: NotFoundError: DOM Exception 8' error in Chrome with video-specific url

  if ($('#container2').length) { $('#container2').attr("src", " "); $('#container2').remove(); }
  if ($('#audio *').length) { $('#audio *').attr("src", " "); $('#audio *').remove(); }
  if ($('.target').length) { $('.target').attr("src", " "); $('.target').empty(); }

  close_drawer();

  if ((window.compat < 1)||(typeof window.compat === 'undefined'))
  {
    $.getScript("/videos/processtimeline.js.php?video="+id+"&ts="+new Date().getTime())
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
  $('body').attr('class','fullscreen');
  var vid_id = {
    argentina: 71726402,
    jharkhand: 71726403, 
    maharashtra: 71726404,
    mongolia: 71726405,
    peru: 71726401,
    tasmania: 71765708,
    uganda: 71765710,
    wales: 71765709 
  };

  //alert('launching plain '+vid_id[id]);
  $('#video').delay(3000).html('<iframe src="http://player.vimeo.com/video/'+vid_id[id]+'?autoplay=1" width="800" height="400" frameborder="0"></iframe>');
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
