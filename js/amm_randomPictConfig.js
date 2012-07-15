/**
 * randomPictConfig
 *
 * release 1.0.0
 */
function randomPictConfig(opt, keys, token, initValues)
{
  var options = {
          ajaxUrl:'plugins/AMenuManager/amm_ajax.php'
        },
      translatedKeys= {
          g002_setting_randompic_periodicchange_deactivated:'g002_setting_randompic_periodicchange_deactivated',
          g002_setting_randompic_height_auto:'g002_setting_randompic_height_auto'
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
        blockTitles:[],
        blockHeight:$("#iamm_rp_height_slider").slider('option', 'value'),
        infosName:$("#iamm_randompicture_showname").inputList('value'),
        infosComment:$("#iamm_randompicture_showcomment").inputList('value'),
        freqDelay:$("#iamm_rp_pc_slider").slider('option', 'value')==99?0:$("#iamm_rp_pc_slider").slider('option', 'value'),
        selectMode:$("#iamm_randompicture_selectedMode").inputRadio('value'),
        selectCat:$("#iamm_randompicture_selectedCat").categorySelector('value')
      };



      list=$('#iamm_randompicture_title').inputText('languagesValues');
      for(var id in list)
      {
        datas.blockTitles.push({id:id, value:list[id]});
      }

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.randomPict.setConfig", token:properties.token, datas:datas },
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
      $("#iamm_randompicture_selectedCat").categorySelector('isValid', true);

      if($("#iamm_randompicture_selectedMode").inputRadio('value')=='c' &&
         $("#iamm_randompicture_selectedCat").categorySelector('value').length==0
        )
      {
        ok=false;
        $("#iamm_randompicture_selectedCat").categorySelector('isValid', false);
      }

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
    $('#iamm_randompicture_selectedMode').inputRadio(
      {
        change:function () { $('#iConfigState').hide(); }
      }
    );
    $('#iamm_randompicture_selectedMode').inputRadio('value', initValues.selectMode);

    $('#iamm_randompicture_selectedCat').categorySelector(
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

    $('#iamm_randompicture_showname').inputList(
      {
        colsWidth:[300],
        popupMode:'mouseout',
        change: function () { $('#iConfigState').hide(); }
      }
    ).inputList('value', initValues.infosName).css('display', 'block');
    $('#iamm_randompicture_showcomment').inputList(
      {
        colsWidth:[300],
        popupMode:'mouseout',
        change: function () { $('#iConfigState').hide(); }
      }
    ).inputList('value', initValues.infosComment).css('display', 'block');


    $('#islang').inputList(
      {
        listMaxHeight:250,
        popupMode:'mouseout',
        change: function () { $('#iConfigState').hide(); }
      }
    ).css('display', 'block');

    $('#iamm_randompicture_title').inputText(
      {
        languages:initValues.langs,
        languagesValues:initValues.blockTitles,
        currentLanguage:initValues.userLang,
        languageSelector:'islang',
        displayChar:50,
        maxChar:255,
        change: function () { $('#iConfigState').hide(); }
      }
    );

    $('#islang').inputList('value', initValues.userLang).css('display', 'block');



    formatDelay(initValues.freqDelay);
    $("#iamm_rp_pc_slider").slider(
      {
        min:0,
        max:60000,
        step:50,
        value:initValues.freqDelay,
        slide: function(event, ui) { formatDelay(ui.value); },
        change: function () { $('#iConfigState').hide(); }
      });
    $("#iamm_rp_pc_slider a").addClass('gcBgInput');

    formatHeight(initValues.blockHeight);
    $("#iamm_rp_height_slider").slider(
      {
        min:99,
        max:300,
        steps:1,
        value:initValues.blockHeight,
        slide: function(event, ui) { formatHeight(ui.value); },
        change: function () { $('#iConfigState').hide(); }
      });
    $("#iamm_rp_height_slider a").addClass('gcBgInput');
  },

  /**
   * format delay value for display
   */
  formatDelay = function(delay)
  {
    $("#iamm_randompicture_periodicchange").val(delay);
    if(delay==0)
    {
      $("#iamm_rp_pc_display").html(translatedKeys.g002_setting_randompic_periodicchange_deactivated);
    }
    else
    {
      $("#iamm_rp_pc_display").html((delay/1000).toFixed(2)+"s");
    }
  },

  formatHeight = function(height)
  {
    var vheight = (height==99)?0:height;

    $("#iamm_randompicture_height").val(vheight);
    if(vheight==0)
    {
      $("#iamm_rp_height_display").html(translatedKeys.g002_setting_randompic_height_auto);
    }
    else
    {
      $("#iamm_rp_height_display").html(vheight+"px");
    }
  };


  $.extend(options, opt);
  $.extend(translatedKeys, keys);

  this.submit = function () { submit(); };

  init(initValues);
}


