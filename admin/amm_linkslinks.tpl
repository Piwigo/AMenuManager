{literal}
<script type="text/javascript">
  var ulm;

  $(window).load(
    function ()
    {
      ulm=new userLinksManage(
        {},
        {
          g002_ok:"{/literal}{'g002_ok'|@translate}{literal}",
          g002_cancel:"{/literal}{'g002_cancel'|@translate}{literal}",
          g002_loading:"{/literal}{'g002_loading'|@translate}{literal}",
          g002_editoflink:"{/literal}{'g002_editoflink'|@translate}{literal}",
          g002_createoflink:"{/literal}{'g002_createoflink'|@translate}{literal}"
        },
        '{/literal}{$token}{literal}'
      );
    }
  );
</script>
{/literal}


<div class='addLink'>
  <a onclick="ulm.edit('');">{'g002_addlink'|@translate}</a>
</div>
<br>

<ul id='iList' class='menuUl'>
</ul>

<div id='iListOrderButtons' style='display:none;'>
  <input id='iBtValid' type='button' value="{'g002_valid_order'|@translate}" onclick='ulm.doUpdateOrder();'>
  <input id='iBtReset' type='button' value="{'g002_reset_order'|@translate}" onclick='ulm.load();'>
</div>




<div id="iDialogDelete">
</div>


<div id="iDialogEdit" dialogTitle="{'g002_editLink'|@translate}">
  <div id='iBDProcessing' style="display:none;position:absolute;width:100%;height:100%;background:#000000;opacity:0.75">
      <img src="plugins/GrumPluginClasses/icons/processing.gif" style="margin-top:100px;">
  </div>

  <table class="formtable">
    <tr>
      <td>{'g002_label'|@translate}</td>
      <td><div id='iamm_label'></div></td>
    </tr>

    <tr>
      <td>{'g002_url'|@translate}</td>
      <td><div id='iamm_url'></div></td>
    </tr>

    <tr>
      <td>{'g002_icon'|@translate}</td>
      <td>
        <div id="iamm_icon" style='display:none;'>
          [
          {foreach from=$datas.iconsValues key=name item=icon name=items}
            {ldelim}"value":"{$icon.value}","cols":["&lt;img src='{$icon.img}'&gt;", "{$icon.label}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
          {/foreach}
          ]
        </div>
      </td>
    </tr>

    <tr>
      <td>{'g002_mode'|@translate}</td>
      <td>
        <div id="iamm_mode" style='display:none;'>
          [
          {foreach from=$datas.modesValues key=name item=mode name=items}
            {ldelim}"value":"{$mode.value}","cols":["{$mode.label}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
          {/foreach}
          ]
        </div>
      </td>
    </tr>

    <tr>
      <td>{'g002_visible'|@translate}</td>
      <td>
        <div id="iamm_visible">
          <label><input type="radio" value="y">&nbsp;{'g002_yesno_y'|@translate}<br></label>
          <label><input type="radio" value="n">&nbsp;{'g002_yesno_n'|@translate}</label>
        </div>
      </td>
    </tr>

    <tr>
      <td>{'g002_accessibility'|@translate}</td>
      <td style='min-width:475px;'>
        <div id="iamm_access">
          <table>
            <tr>
              <td style='width:50%;font-weight:bold;'>{'User'|@translate}</td>
              <td style='width:50%;font-weight:bold;'>{'Group'|@translate}</td>
            </tr>
            <tr>
              <td>
                <div id='iamm_access_users'>
                {foreach from=$datas.access.users item=values}
                  <label><input type="checkbox" value="{$values.id}" class='visibilityUser' />&nbsp;{$values.name}</label><br/>
                {/foreach}
                </div>
              </td>
              <td>
                <div id='iamm_access_groups'>
                {foreach from=$datas.access.groups item=values}
                  <label><input type="checkbox" value="{$values.id}" class='visibilityGroup' />&nbsp;{$values.name}</label><br/>
                {/foreach}
                </div>
              </td>
            </tr>
          </table>


        </div>
      </td>
    </tr>

  </table>

</div>

