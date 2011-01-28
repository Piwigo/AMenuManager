
{literal}


<script type="text/javascript">
  var ac;

  $(window).load(
    function ()
    {
      ac=new albumConfig(
        {},
        {
          g002_setting_randompic_periodicchange_deactivated:"{/literal}{'g002_setting_randompic_periodicchange_deactivated'|@translate}{literal}",
          g002_setting_randompic_height_auto:"{/literal}{'g002_setting_randompic_height_auto'|@translate}{literal}"
        },
        '{/literal}{$token}{literal}',
        {
          'selectCat':{/literal}{$datas.albums}{literal}
        }
      );
    }
  );

</script>
{/literal}

<div id='iBDProcessing' style="display:none;position:absolute;left:0;top:0;width:100%;height:100%;background:#000000;opacity:0.75;z-index:800;">
  <img src="plugins/GrumPluginClasses/icons/processing.gif" style="margin-top:20%;">
</div>

<div id='iConfigState' style='display:none;'>
</div>

<form method="post" action="" class="general">
  <fieldset>
    <legend>{'g002_setting_albums_menus'|@translate}</legend>

      <table class="formtable">
        <tr>
          <td>
            <div id='iamm_album_selectedCat'></div>
          </td>
        </tr>
      </table>
  </fieldset>

  <p>
    <input type="button" id="iamm_submit_apply" value="{'g002_apply'|@translate}" onclick="ac.submit();">
  </p>


</form>
