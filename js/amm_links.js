/**
 * userLinksManage
 *
 * release 1.0.0
 */
function userLinksManage (opt, keys, token)
{
  var options = {
          ajaxUrl:'plugins/AMenuManager/amm_ajax.php'
        },
      translatedKeys= {
          g002_ok:'g002_ok',
          g002_cancel:'g002_cancel',
          g002_loading: 'g002_loading',
          g002_editoflink : 'g002_editoflink',
          g002_createoflink : 'g002_createoflink'
        },
      properties = {
          id:'',
          token:token
        },

  /**
   * load links list
   */
  load = function ()
    {
      $("#iList table.littlefont").sortable('destroy');
      $('#iList').html("<br>"+translatedKeys.g002_loading+"<br><img src='./plugins/GrumPluginClasses/icons/processing.gif'>");

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.links.list", token:properties.token },
          success:
            function(msg)
            {
              $("#iList").html(msg);
              $('#iListOrderButtons').css("display", 'none');

              $("#iList").sortable(
                {
                  connectWith: '.connectedSortable',
                  axis: "y",
                  cursor: 'move',
                  opacity:0.6,
                  items: 'li',
                  tolerance:'pointer',
                  update: function () {  $('#iListOrderButtons').css("display", 'block'); }
                }
              );
            }
        }
      );
    },

  /**
   * edit or create a new link
   *
   * @param String linkId : if empty, assume to create a new link
   */
  edit = function (linkId)
    {
      properties.id=linkId;

      $('#iDialogEdit')
        .dialog('option', 'title', (linkId=='')?translatedKeys.g002_createoflink:translatedKeys.g002_editoflink)
        .dialog("open");
    },

  /**
   * remove a link
   *
   * @param String linkId : link to remove
   */
  remove = function (linkId)
    {
      properties.id=linkId;

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.links.delete", id:properties.id, token:properties.token },
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
        $('#iamm_label').inputText('value', '');
        $('#iamm_url').inputText('value', '');
        $('#iamm_icon').inputList('value', ':first');
        $('#iamm_mode').inputList('value', ':first');
        $('#iamm_visible').inputRadio('value', 'y');
        $('#iamm_access_users').inputCheckbox('value', ':all');
        $('#iamm_access_groups').inputCheckbox('value', ':all');
      }
      else
      {
        tmp=$.parseJSON(items);

        $('#iamm_label').inputText('value', tmp.label);
        $('#iamm_url').inputText('value', tmp.url);
        $('#iamm_icon').inputList('value', tmp.icon);
        $('#iamm_mode').inputList('value', tmp.mode);
        $('#iamm_visible').inputRadio('value', tmp.visible);
        $('#iamm_access_users').inputCheckbox('value', '', tmp.accessUsers);
        $('#iamm_access_users').inputCheckbox('value', ':invert');
        $('#iamm_access_groups').inputCheckbox('value', '', tmp.accessGroups);
        $('#iamm_access_groups').inputCheckbox('value', ':invert');
      }
    },

  /**
   * update order on server
   */
  doUpdateOrder = function ()
    {
      var datas={
            links:[]
          },
          order=0;

      $('#iList li.connectedSortable').each(
        function ()
        {
          datas.links.push(
            {
              id:$(this).attr('linkId'),
              order:order
            }
          );
          order++;
        }
      );

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.links.order", token:properties.token, datas:datas },
          success:
            function(msg)
            {
              displayProcessing(false);

              returned=msg.split('!');

              if(returned[0]=='OK')
              {
                $('#iListOrderButtons').css("display", 'none');
              }
              else
              {
                $('#'+returned[0]).addClass('error');
                alert(returned[1]);
              }
            }
        }
      );
    },

  /**
   * update values on server
   */
  doUpdate = function ()
    {
      displayProcessing(true);

      // build datas
      var datas = {
        label:$('#iamm_label').inputText('value'),
        url:$('#iamm_url').inputText('value'),
        icon:$('#iamm_icon').inputList('value'),
        mode:$('#iamm_mode').inputList('value'),
        visible:$('#iamm_visible').inputRadio('value'),
        accessUsers:$('#iamm_access_users').inputCheckbox('value'),
        accessGroups:$('#iamm_access_groups').inputCheckbox('value')
      };

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.links.set", id:properties.id, token:properties.token, datas:datas },
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
  init = function ()
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
          width:600,
          height:400,
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
                data: { ajaxfct:"admin.links.get", id:properties.id, token:properties.token },
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

    $('#iamm_label').inputText(
      {
        displayChar:50,
        maxChar:50
      }
    );

    $('#iamm_url').inputText(
      {
        displayChar:50,
        maxChar:255
      }
    );

    $('#iamm_icon').inputList(
      {
        listMaxWidth:250,
        colsWidth:[22,200],
        colsCss:['iconColImg','iconColText'],
        popupMode:'mouseout'
      }
    ).css('display', 'block');

    $('#iamm_mode').inputList({popupMode:'mouseout'}).css('display', 'block');

    $('#iamm_visible').inputRadio();

    $('#iamm_access_users').inputCheckbox({returnMode:'notSelected'});
    $('#iamm_access_groups').inputCheckbox({returnMode:'notSelected'});

    load();
  };

  $.extend(options, opt);
  $.extend(translatedKeys, keys);

  this.load = function () { load(); };
  this.edit = function (linkId) { edit(linkId); };
  this.remove = function (linkId) { remove(linkId); };
  this.doUpdateOrder = function () { doUpdateOrder(); };

  init();
}


