/**
 * albumConfig
 *
 * release 1.0.0
 */
function albumConfig(opt, keys, token, initValues)
{
  var options = {
          ajaxUrl:'plugins/AMenuManager/amm_ajax.php'
        },
      translatedKeys= {
        },
      properties = {
          token:token
        },


  /**
   * submit config
   */
  submit = function ()
    {
      if(!checkValidity()) return(false);

      displayProcessing(true);

      // build datas
      var datas = {
        selectCat:$("#iamm_album_selectedCat").categorySelector('value')
      };


      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.album.setConfig", token:properties.token, datas:datas },
          success:
            function(msg)
            {
              displayProcessing(false);

              returned=msg.split('!');

              if(returned[0]=='OK')
              {
                $('#iConfigState')
                  .html(returned[1])
                  .removeClass('errors')
                  .addClass('infos')
                  .css('display', 'block');
              }
              else
              {
                $('#iConfigState')
                  .html(returned[1])
                  .removeClass('infos')
                  .addClass('errors')
                  .css('display', 'block');
              }
            }
        }
      );
    },

  /**
   * check validity of form
   */
  checkValidity = function ()
    {
      $('.error').removeClass('error');
      ok=true;

      return(ok);
    },

  /**
   * display or hide the processing flower
   */
  displayProcessing = function (visible)
    {
      if(visible)
      {
        $('#iBDProcessing').css("display", "block");
      }
      else
      {
        $('#iBDProcessing').css("display", "none");
      }
    },

  /**
   * initialize the object and page form
   */
  init = function (initValues)
  {
    $('#iamm_album_selectedCat').categorySelector(
      {
        serverUrl:'plugins/GrumPluginClasses/gpc_ajax.php',
        listMaxWidth:650,
        listMaxHeight:550,
        userMode:'public',
        galleryRoot:false,
        displayStatus:false,
        filter:'all',
        multiple:true,
        load:
          function (event)
          {
            $(this)
              .categorySelector('collapse', ':all')
              .categorySelector('value', initValues.selectCat);
          },
        change: function () { $('#iConfigState').hide(); }
      }
    );
  };


  $.extend(options, opt);
  $.extend(translatedKeys, keys);

  this.submit = function () { submit(); };

  init(initValues);
}


