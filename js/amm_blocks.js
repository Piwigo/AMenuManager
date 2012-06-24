
function coreBlocks(opt, keys, token, initValues)
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
          token:token,
          resetValues:
            {
              'piwigo':[],
              'cancel':[]
            }
        },

  /**
   * display tab content
   */
  displayTabContent = function (tab)
    {
      switch(tab)
      {
        case 'position':
          $('#containerPos').css('display', 'block');
          $('#containerMenu').css('display', 'none');
          break;
        case 'blocksContent':
          $('#containerPos').css('display', 'none');
          $('#containerMenu').css('display', 'block');
          break;
      }
    }

  /**
   * reset values
   * @param String mode : 'piwigo' (piwigo default value) or 'cancel' (restore previous settings)
   */
  reset = function(mode)
    {
      $('#iConfigState').hide();

      for(var i=0;i<properties.resetValues[mode].length;i++)
      {
        var access=resetValues[mode][i].visibility.split('/'),
            accessUsers=access[0].split(','),
            accessGroup=access[1].split(',');

        $("#menu_"+resetValues[mode][i].block).get(0).appendChild($("#i"+resetValues[mode][i].id).get(0));

        $("#i"+resetValues[mode][i].id).find('input.visibilityUser').each(
          function ()
          {
            $(this).attr('checked', $.inArray($(this).attr('value'), accessUsers)==-1);
          }
        );

        $("#i"+resetValues[mode][i].id).find('input.visibilityGroup').each(
          function ()
          {
            $(this).attr('checked', $.inArray($(this).attr('value'), accessGroup)==-1);
          }
        );
      }
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
            menuItems: {},
            blocks: {}
          },
          order=0;

      $('#containerMenu ul.categoryUl li.categoryLi').each(
        function ()
        {
          var visibilityUser=[]
              visibilityGroup=[];

          $(this).find('input.visibilityUser:not(:checked)').each(
            function ()
            {
              visibilityUser.push($(this).attr('value'));
            }
          );
          $(this).find('input.visibilityGroup:not(:checked)').each(
            function ()
            {
              visibilityGroup.push($(this).attr('value'));
            }
          );

          datas.menuItems[this.id.substr(1)]={
            order:order,
            container:$(this).parent().parent().attr('id').substr(14),
            visibilityUser:visibilityUser,
            visibilityGroup:visibilityGroup
          };
          order++;
        }
      );

      order=0;
      $('ul.menuUl li.menuListItem').each(
        function ()
        {
          var id=$(this).attr('blockid'),
              visibilityUser=$(this).find('div.menuListUsers').inputList('value'),
              visibilityGroup=$(this).find('div.menuListGroups').inputList('value');

          datas.blocks[id]={
            id:id,
            order:order,
            users:visibilityUser,
            groups:visibilityGroup
          };

          order++;
        }
      );

      $.ajax(
        {
          type: "POST",
          url: options.ajaxUrl,
          async: true,
          data: { ajaxfct:"admin.coreBlocks.setConfig", token:properties.token, datas:datas },
          success:
            function(msg)
            {
              displayProcessing(false);

              returned=msg.split('!');

              if(returned[0]=='OK')
              {
                properties.resetValues.cancel=[];
                for(var id in datas.items)
                {
                  properties.resetValues.cancel.push(
                    {
                      id:id,
                      block:datas.items[id].container,
                      order:datas.items[id].order,
                      visibility:datas.items[id].visibility
                    }
                  );
                }

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

  init = function (initValues)
    {
      properties.resetValues=initValues.resetValues;

      $("#containerMenu").sortable(
        {
          connectWith: '.connectedSortable',
          cursor: 'move',
          opacity:0.6,
          items: 'li:not(.menuItemDisabled)',
          tolerance:'pointer',
          start: function ()
            {
              $('#iConfigState').hide();
            }
        }
      );

      $('#containerMenu ul.categoryUl li.categoryLi img.visibilitySwitch').bind('click',
        function ()
        {
          $('#'+$(this).parent().parent().attr('id')+'_visibility').toggle();
          $('#iConfigState').hide();
        }
      );

      $("ul.menuUl").sortable(
        {
          connectWith: '.connectedSortable',
          axis: "y",
          cursor: 'move',
          opacity:0.6,
          items: 'li.connectedSortable',
          tolerance:'pointer',
          start: function ()
            {
              $('#iConfigState').hide();
            }
        }
      );

      $('ul.menuUl li div.menuListUsers, ul.menuUl li div.menuListGroups').inputList(
        {
          listMaxHeight:300,
          multiple:true,
          popupMode:'mouseout',
          returnMode:'notSelected',
          change: function ()
            {
              $('#iConfigState').hide();
            }
        }
      ).inputList('value', ':invert').css('display', 'block');


      displayTabContent(initValues.tab);
    };

  this.reset = function (mode) { reset(mode); };
  this.submit = function () { submit(); };
  this.displayTabContent = function (tab) { displayTabContent(tab); };

  init(initValues);
}


