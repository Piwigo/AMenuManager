/**
 * userPersonnalisedBlockManage
 *
 * release 1.0.2
 */
function userPersonnalisedBlockManage(opt, keys, token, initValues)
{
  var options = {
          ajaxUrl:'plugins/AMenuManager/amm_ajax.php'
        },
      translatedKeys= {
          g002_ok:'g002_ok',
          g002_cancel:'g002_cancel',
          g002_loading: 'g002_loading',
          g002_editofpersonalised : 'g002_editofpersonalised',
          g002_createofpersonalised : 'g002_createofpersonalised'
        },
      properties = {
          id:'',
          token:token
        },

  /**
   * load blocks list
   */
  load = function ()
    {
      $('#iList').html("<br>"+translatedKeys.g002_loading+"<br><img src='./plugins/GrumPluginClasses/icons/processing.gif'>");

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.blocks.list", token:properties.token },
          success:
            function(msg)
            {
              $("#iList").html(msg);
            }
        }
      );
    },

  /**
   * edit or create a new block
   *
   * @param String blockId : if empty, assume to create a new block
   */
  edit = function (blockId)
    {
      properties.id=blockId;

      $('#iDialogEdit')
        .dialog('option', 'title', (blockId=='')?translatedKeys.g002_createofpersonalised:translatedKeys.g002_editofpersonalised)
        .dialog("open");
    },

  /**
   * remove a block
   *
   * @param String blockId : block to remove
   */
  remove = function (blockId)
    {
      properties.id=blockId;

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.blocks.delete", id:properties.id, token:properties.token },
          success:
            function(msg)
            {
              load();
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
/*
      if($('#iamm_label').inputText('value')=='')
      {
        $('#iamm_label').inputText('isValid', false);
        ok=false;
      }

      if($('#iamm_url').inputText('value')=='')
      {
        $('#iamm_url').inputText('isValid', false);
        ok=false;
      }
*/
      return(ok);
    },


  /**
   * update values of the dialog box
   *
   * @param String items : json string ; if empty assume to reset all fields
   *                       with default values
   */
  updateDialog = function (items)
    {
      if(items=='')
      {
        $('#iamm_personalised_nfo').inputText('value', '');
        $('#iamm_personalised_title')
          .inputText('value', '')
          .inputText('languagesValues', ':clear');
        $('#iamm_personalised_content')
          .inputText('value', '')
          .inputText('languagesValues', ':clear');
        $('#iamm_personalised_visible').inputRadio('value', 'y');
      }
      else
      {
        var tmp=$.parseJSON(items),
            titles={},
            contents={};

        for(var lang in tmp.langs)
        {
          titles[lang]=tmp.langs[lang].title;
          contents[lang]=tmp.langs[lang].content;
        }

        $('#iamm_personalised_nfo').inputText('value', tmp.nfo);
        $('#iamm_personalised_title').inputText('languagesValues', ':clear').inputText('languagesValues', titles);
        $('#iamm_personalised_content').inputText('languagesValues', ':clear').inputText('languagesValues', contents);
        $('#iamm_personalised_visible').inputRadio('value', tmp.visible);
      }
    },

  /**
   * update values on server
   */
  doUpdate = function ()
    {
      displayProcessing(true);

      // build datas
      var langs=[],
          titles=$('#iamm_personalised_title').inputText('languagesValues'),
          contents=$('#iamm_personalised_content').inputText('languagesValues');

      for(var lang in titles)
      {
        langs.push(
          {
            lang:lang,
            title:titles[lang],
            content:contents[lang]
          }
        );
      }




      var datas = {
        nfo:$('#iamm_personalised_nfo').inputText('value'),
        visible:$('#iamm_personalised_visible').inputRadio('value'),
        langs:langs
      };

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.blocks.set", id:properties.id, token:properties.token, datas:datas },
          success:
            function(msg)
            {
              displayProcessing(false);

              if(msg.match(/^[0-9]+$/i)!=null)
              {
                // result Ok ! => close the dialog box and reload the list
                $('#iDialogEdit').dialog("close");
                load();
              }
              else
              {
                returned=msg.split('!');
                $('#'+returned[0]).addClass('error');
                alert(returned[1]);
              }
            }
        }
      );
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
    var buttons={};

    buttons[translatedKeys.g002_ok]=function()
      {
        if(checkValidity()) doUpdate();
      };
    buttons[translatedKeys.g002_cancel]=function()
      {
        $('#iDialogEdit').dialog("close");
      };

    $('#iDialogEdit')
      .dialog(
        {
          autoOpen:false,
          width:800,
          height:480,
          modal: true,
          dialogClass: 'gcBgTabSheet gcBorder',
          title: '',
          buttons:buttons
        }
      )
      .bind('dialogopen', function ()
        {
          if(properties.id!='')
          {
            displayProcessing(true);

            $.ajax(
              {
                type: "POST",
                url: options.ajaxUrl,
                async: true,
                data: { ajaxfct:"admin.blocks.get", token:properties.token, id:properties.id },
                success:
                  function(msg)
                  {
                    updateDialog(msg);
                    displayProcessing(false);
                  }
              }
            );
          }
          else
          {
            updateDialog('');
          }
        }
      );

    $('#islang').inputList({popupMode:'mouseout', listMaxHeight:250});


    $('#iamm_personalised_nfo').inputText(
      {
        displayChar:75,
        maxChar:255
      }
    );

    $('#iamm_personalised_title').inputText(
      {
        displayChar:75,
        maxChar:255,
        languages:initValues.langs,
        currentLanguage:initValues.userLang,
        languageSelector:'islang'
      }
    );

    $('#iamm_personalised_content').inputText(
      {
        multilines:true,
        displayChar:70,
        numRows:13,
        languages:initValues.langs,
        currentLanguage:initValues.userLang,
        languageSelector:'islang'
      }
    );

    $('#islang').inputList('value', initValues.userLang);

    $('#iamm_personalised_visible').inputRadio();

    load();
  };

  $.extend(options, opt);
  $.extend(translatedKeys, keys);

  this.load = function () { load(); };
  this.edit = function (linkId) { edit(linkId); };
  this.remove = function (linkId) { remove(linkId); };

  init(initValues);
}


