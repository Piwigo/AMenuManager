/**
 * randomPict v1.0.1
 *
 * | release | date       |
 * | 1.0.1   | 2011-05-24 | * mantis bug:2311
 * |         |            |   . broken javascript if random pic set is empty 
 * |         |            |
 */
function randomPict(opt)
{
  var options={
          fixedHeight:0,
          pictures:[],
          showName:'n',
          showComment:'n',
          delay:0
        },
      properties={
          vIntervalID:0,
          vCurrentPict:0,
          nameDOM:'',
          commentDOM:'',
          img:new Image()
        },

  init = function (opt)
  {
    $.extend(options, opt);

    switch(options.showName)
    {
      case 'o':
        properties.nameDOM='iammRPicNameO';
        break;
      case 'u':
        properties.nameDOM='iammRPicNameU';
        break;
    }
    switch(options.showComment)
    {
      case 'o':
        properties.commentDOM='iammRPicCommentO';
        break;
      case 'u':
        properties.commentDOM='iammRPicCommentU';
        break;
    }

    if(properties.nameDOM!='') $('#'+properties.nameDOM).css('display', 'block');
    if(properties.commentDOM!='') $('#'+properties.commentDOM).css('display', 'block');

    if(options.delay>0) properties.vIntervalID = window.setInterval(getNextPicture, options.delay);

    if(options.fixedHeight>0) $('#irandompicinner').css('height', options.fixedHeight+'px');

    preloadImages();
  },

  preloadImages = function ()
  {
    $(properties.img).bind('load',
      function ()
      {
        properties.vCurrentPict++;
        if(properties.vCurrentPict>=options.pictures.length)
        {
          properties.vCurrentPict=-1;
          getNextPicture();
        }
        else
        {
          properties.img.src=options.pictures[properties.vCurrentPict].thumb;
        }
      }
    );
    properties.img.src=options.pictures[properties.vCurrentPict].thumb;
  },

  computePositionTop = function()
  {
    $("#iamm_ill0").css({top:($('#irandompicinner').innerHeight()-$("#iamm_ill0").innerHeight())/2});
  },

  getNextPicture = function()
  {
    properties.vCurrentPict++;
    if(properties.vCurrentPict>=options.pictures.length) properties.vCurrentPict=0;

    $('#iamm_ill0').fadeTo(200, 0,
      function ()
      {
        if(properties.nameDOM!='') $('#'+properties.nameDOM).html(options.pictures[properties.vCurrentPict].name);
        if(properties.commentDOM!='') $('#'+properties.commentDOM).html(options.pictures[properties.vCurrentPict].comment);

        $('#iammRPicLink').attr('href', options.pictures[properties.vCurrentPict].link);
        $('#iammRPicImg').attr('src', options.pictures[properties.vCurrentPict].thumb);
        computePositionTop();
        $('#iamm_ill0').fadeTo(200, 1);
      }
    );

  };

  init(opt);
}


$(document).ready(
  function ()
  {    
    var rPict;
    if(typeof randomPictOpt!=='undefined')
    {
      rPict=new randomPict(randomPictOpt);
    }
    else
    {
      $('#mbAMM_randompict').remove();
    }
  }
);
