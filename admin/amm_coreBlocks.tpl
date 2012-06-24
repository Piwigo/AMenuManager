{literal}
<script type="text/javascript">

  var cbm,
      resetValues = {
        'cancel':new Array(),
        'piwigo':new Array()
      };
  $(window).load(
    function ()
    {
      {/literal}
      // initialization for cancel et reset functions
      {foreach from=$datas.coreBlocks.defaultValues item=value key=id}
        resetValues.piwigo.push( {literal} { {/literal} id:"{$id}", block:"{$value.container}", order:{$value.order}, visibility:"{$value.visibility}" {literal} } {/literal} );
      {/foreach}
      {foreach from=$datas.coreBlocks.items item=value key=id}
        resetValues.cancel.push( {literal} { {/literal} id:"{$id}", block:"{$value.container}", order:{$value.order}, visibility:"{$value.visibility}" {literal} } {/literal} );
      {/foreach}
      {literal}

      cbm=new coreBlocks(
        {},
        {},
        '{/literal}{$token}{literal}',
        {
          resetValues:resetValues,
          tab:"{/literal}{$datas.tab}{literal}"
        }

      );
    }
  );
</script>
{/literal}


<div style='padding-top:15px;'>
{$blocksTabsheet}
</div>


<div id="containerPos" style='display:none;'>

  <ul class='menuUl'>
    {foreach from=$datas.menuBlocks item=block}
      <li class='menuListItem connectedSortable pluginBox' blockId='{$block.id}'>

        <table class='menuListAccess'>
          <tr>
            <td style='min-width: 250px;'>
              <span class='menuListMove' title="{'Drag to re-order'|@translate}">&nbsp;</span>
              <span class='menuListName'>
                <span style='font-weight:bold;' class='pluginBoxNameCell'>{$block.name|@translate}</span>&nbsp;[{$block.id}]<br>
                <span style='font-style:italic;'>{if $block.owner=='piwigo'}Piwigo{else}{'g002_plugin'|@translate}&nbsp;:&nbsp;{$block.owner}{/if}</span>
              </span>
            </td>
            <td style='min-width: 100px;text-align:right;'>{'g002_accessibility'|@translate}</td>
            <td style='width:30%;'>
              <div id='users_{$block.id}' class='menuListUsers' style='display:none;'>
              {ldelim}
                "selected":{$block.users},
                "values":
                [
                {foreach from=$datas.users item=user name=items}
                  {ldelim}"value":"{$user.id}","cols":["{$user.name}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
                {/foreach}
                ]
              {rdelim}
              </div>
            </td>
            <td style='width:30%;'>
              <div id='groups_{$block.id}' class='menuListGroups' style:'display:none;'>
              {ldelim}
                "selected":{$block.groups},
                "values":
                [
                {foreach from=$datas.groups item=group name=items}
                  {ldelim}"value":"{$group.id}","cols":["{$group.name}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
                {/foreach}
                ]
              {rdelim}
              </div>
            </td>

          </tr>
        </table>
      </li>
    {/foreach}
  </ul>

</div>

<div id="containerMenu" style='display:none;'>
  <table>
    <tr>
      {foreach from=$datas.coreBlocks.blocks item=blockName key=block}
      <td>
        <h3>{$blockName|@translate}</h3>
      </td>
      {/foreach}
      <td>&nbsp;</td>
    </tr>
    <tr>
    {foreach from=$datas.coreBlocks.blocks item=blockName key=block name=items}
      <td {if !$smarty.foreach.items.first}class='leftBar'{/if}>
        <div id="containerMenu_{$block}" class="containerMenuBlock">

          <ul class="connectedSortable categoryUl" id="menu_{$block}">
          {foreach from=$datas.coreBlocks.items item=data key=id}
            {if $data.container==$block}
              <li class="categoryLi menuItem pluginBox {if $id=='qsearch'}menuItemDisabled{/if}" id="i{$id}">
                <div class='pluginBoxNameCell'>
                  {if $id=="qsearch"}
                  {else}
                  <span class='listMove' title="{'Drag to re-order'|@translate}"></span>
                  {/if}
                  {$data.translation|@translate}

                    <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/permissions.png"
                         class="button drag_button visibilitySwitch"
                         alt="{'g002_click_to_manage_rights'|@translate}"
                         title="{'g002_click_to_manage_rights'|@translate}"
                         style="float:right;"/>

                </div>
                <div id="i{$id}_visibility" class="visibility">
                  {$data.visibilityForm}
                </div>
              </li>
            {/if}
          {/foreach}
          </ul>
        </div>
      </td>
    {/foreach}
    <td class='leftBar'>
      <div class='containerMenuBlock'>
        <input type="button" value="{'g002_cancel'|@translate}" onclick="cbm.reset('cancel');"/><br>
        <input type="button" value="{'g002_piwigo_default'|@translate}" onclick="cbm.reset('piwigo');"/>
      </div>
    </td>
    </tr>
  </table>
</div>

<div style='padding-top:30px;'>
  <input style='margin-left:20px;' type="button" value="{'g002_apply'|@translate}" onclick="cbm.submit();">
</div>

