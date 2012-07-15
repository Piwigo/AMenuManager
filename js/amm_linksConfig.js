/**
 * userLinksConfig
 *
 * release 1.0.0
 */
function userLinksConfig(opt, keys, token, initValues)
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
        title:[],
        showIcons:$('#iamm_links_show_icons').inputRadio('value')
      };

      list=$('#iamm_links_title').inputText('languagesValues');
      for(var id in list)
      {
        datas.title.push({id:id, value:list[id]});
      }

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.links.setConfig", token:properties.token, datas:datas },
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
   * initialize the object
   */
  init = function (initValues)
  {
    $('#iamm_links_show_icons').inputRadio();
    $('#iamm_links_show_icons').inputRadio('value', initValues.showIcons);

    $('#islang').inputList(
      {
        popupMode:'mouseout',
        listMaxHeight:250
      }
    );

    $('#iamm_links_title').inputText(
      {
        languages:initValues.langs,
        languagesValues:initValues.titles,
        currentLanguage:initValues.userLang,
        languageSelector:'islang',
        displayChar:40
      }
    );

    $('#islang').inputList('value', initValues.userLang);
  };

  $.extend(options, opt);
  $.extend(translatedKeys, keys);

  this.submit = function () { submit(); };

  init(initValues);
}


