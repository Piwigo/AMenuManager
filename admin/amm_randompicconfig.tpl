{literal}
<style>
 .ui-slider {
    width:360px;
    height:10px;
    border-width:1px;
    border-style:solid;
    margin-right:5px;
    padding-right:14px;
  }
 .ui-slider-handle {
    width:12px;
    height:12px;
    position:relative;
    top:-2px;
    border-width:1px;
    border-style:solid;
    display:block;
  }
</style>

<script type="text/javascript">
  var rpc;

  $(window).load(
    function ()
    {
      rpc=new randomPictConfig(
        {},
        {
          g002_setting_randompic_periodicchange_deactivated:"{/literal}{'g002_setting_randompic_periodicchange_deactivated'|@translate}{literal}",
          g002_setting_randompic_height_auto:"{/literal}{'g002_setting_randompic_height_auto'|@translate}{literal}"
        },
        '{/literal}{$token}{literal}',
        {
          'selectMode':"{/literal}{$datas.config.selectMode}{literal}",
          'selectCat':{/literal}{$datas.config.selectCat}{literal},
          'infosName':"{/literal}{$datas.config.infosName}{literal}",
          'infosComment':"{/literal}{$datas.config.infosComment}{literal}",
          'freqDelay':"{/literal}{$datas.config.freqDelay}{literal}",
          'blockHeight':"{/literal}{$datas.config.blockHeight}{literal}",
          'blockTitles':
            {
            {/literal}
            {foreach from=$datas.config.blockTitles name=items key=langCode item=title}
              "{$langCode}":"{$title}"{if !$smarty.foreach.items.last},{/if}
            {/foreach}
            {literal}
            },
          'userLang':'{/literal}{$datas.selectedLang}{literal}',
          'langs':[
              {/literal}
              {foreach from=$datas.config.blockTitles name=items key=langCode item=title}
                "{$langCode}"{if !$smarty.foreach.items.last},{/if}
              {/foreach}
              {literal}
            ]
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
    <legend>{'g002_setting_block_menu'|@translate}</legend>

    <table class="formtable">
      <tr>
        <td>{'g002_setting_block_title'|@translate}</td>
        <td colspan="2">
          <div id="iamm_randompicture_title"></div>

          <div id="islang" style='display:none;'>
            [
            {foreach from=$datas.langs key=langCode item=langLabel name=items}
              {ldelim}"value":"{$langCode}","cols":["{$langLabel}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
            {/foreach}
            ]
          </div>
        </td>
      </tr>

      <tr>
        <td>{'g002_setting_randompic_height'|@translate}</td>
        <td>
          <div id="iamm_rp_height_slider" class="gcBgInput gcBorderInput"></div>
        </td>
        <td width="90px">
          <div id="iamm_rp_height_display"></div>
        </td>
      </tr>


    </table>

  </fieldset>

  <fieldset>
    <legend>{'g002_setting_randompic_aboutpicture'|@translate}</legend>
      <table class="formtable">
        <tr>
          <td>{'g002_setting_randompic_showname'|@translate}</td>
          <td>
            <div id="iamm_randompicture_showname" style='display:none;'>
              [
                {ldelim}"value":"n","cols":["{'g002_show_n'|@translate}"]{rdelim},
                {ldelim}"value":"o","cols":["{'g002_show_o'|@translate}"]{rdelim},
                {ldelim}"value":"u","cols":["{'g002_show_u'|@translate}"]{rdelim}
              ]
            </div>
          </td>
        </tr>

        <tr>
          <td>{'g002_setting_randompic_showcomment'|@translate}</td>
          <td>
            <div id="iamm_randompicture_showcomment" style='display:none;'>
              [
                {ldelim}"value":"n","cols":["{'g002_show_n'|@translate}"]{rdelim},
                {ldelim}"value":"o","cols":["{'g002_show_o'|@translate}"]{rdelim},
                {ldelim}"value":"u","cols":["{'g002_show_u'|@translate}"]{rdelim}
              ]
            </div>
          </td>
        </tr>

      </table>
  </fieldset>


  <fieldset>
    <legend>{'g002_setting_randompic_periodicchange'|@translate}</legend>
      <table class="formtable">
        <tr>
          <td>{'g002_setting_randompic_periodicchange_delay'|@translate}</td>
          <td>
            <div id="iamm_rp_pc_slider" class="gcBgInput gcBorderInput"></div>
          </td>
          <td width="70px">
            <div id="iamm_rp_pc_display"></div>
          </td>
        </tr>
      </table>
  </fieldset>


  <fieldset>
    <legend>{'g002_selectedpict'|@translate}</legend>
      <table class="formtable">
        <tr>
          <td>
            <div id='iamm_randompicture_selectedMode'>
              <label><input type="radio" value="a">&nbsp;{'g002_selected_all_gallery'|@translate}<br></label>
              <label><input type="radio" value="f">&nbsp;{'g002_selected_favorites_wm'|@translate}<br></label>
              <label><input type="radio" value="c">&nbsp;{'g002_selected_categories'|@translate}<br></label>
                <div id='iamm_randompicture_selectedCat'></div>
            </div>
          </td>
        </tr>
      </table>
  </fieldset>

  <p>
    <input type="button" id="iamm_submit_apply" value="{'g002_apply'|@translate}" onclick="rpc.submit();">
  </p>


</form>
