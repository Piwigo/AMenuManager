{literal}
<script type="text/javascript">
  var upbm;

  $(window).load(
    function ()
    {
      upbm=new userPersonnalisedBlockManage(
        {},
        {
          g002_ok:"{/literal}{'g002_ok'|@translate}{literal}",
          g002_cancel:"{/literal}{'g002_cancel'|@translate}{literal}",
          g002_loading:"{/literal}{'g002_loading'|@translate}{literal}",
          g002_editofpersonalised:"{/literal}{'g002_editofpersonalised'|@translate}{literal}",
          g002_createofpersonalised:"{/literal}{'g002_createofpersonalised'|@translate}{literal}"
        },
        '{/literal}{$token}{literal}',
        {
          'userLang':'{/literal}{$datas.selectedLang}{literal}',
          'langs':[
              {/literal}
              {foreach from=$datas.langs name=items key=langCode item=label}
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


<div class='addBlock'>
  <a onclick="upbm.edit('');">{'g002_addsection'|@translate}</a>
</div>


<table id='iHeaderList' class="littlefont">
  <tr>
    <th>{'g002_setting_personalised_nfo'|@translate}</th>
    <th style='width:40%;'>{'g002_title'|@translate}</th>
    <th style='width:80px;'>{'g002_visible'|@translate}</th>
    <th style='width:40px;'>&nbsp;</th>
  </tr>
</table>


<div id='iList' class="{$themeconf.name}">
</div>
<div id="iListNb"></div>




<div id="iDialogDelete">
</div>


<div id="iDialogEdit" dialogTitle="{'g002_editLink'|@translate}">
  <div id='iBDProcessing' style="display:none;position:absolute;width:100%;height:100%;background:#000000;opacity:0.75">
      <img src="plugins/GrumPluginClasses/icons/processing.gif" style="margin-top:100px;">
  </div>

  <table class="formtable">

    <tr>
      <th class='gcText2' colspan='2'>
        {'g002_setting_personalised_properties'|@translate}
      </th>
    </tr>

    <tr>
      <td>{'g002_setting_personalised_nfo'|@translate}</td>
      <td><div id='iamm_personalised_nfo'></div></td>
    </tr>

    <tr>
      <td>{'g002_setting_block_active'|@translate}</td>
      <td>
        <div id='iamm_personalised_visible'>
          <label><input type="radio" value="y">&nbsp;{'g002_yesno_y'|@translate}<br></label>
          <label><input type="radio" value="n">&nbsp;{'g002_yesno_n'|@translate}</label>
        </div>
      </td>
    </tr>


    <tr>
      <td colspan='2'>
        &nbsp;
      </td>
    </tr>

    <tr>
      <th class='gcText2' colspan='2'>
        {'g002_setting_block_menu'|@translate}
      </th>
    </tr>


    <tr style='border-bottom:1px dotted;'>
      <td>{'g002_setting_block_langchoice'|@translate}</td>
      <td>
        <div id="islang">
          [
          {foreach from=$datas.langs key=langCode item=langLabel name=items}
            {ldelim}"value":"{$langCode}","cols":["{$langLabel}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
          {/foreach}
          ]
        </div>
      </td>
    </tr>

    <tr>
      <td>{'g002_setting_block_title'|@translate}</td>
      <td>
        <div id="iamm_personalised_title"></div>
      </td>
    </tr>

    <tr>
      <td>{'g002_setting_personalised_content'|@translate}</td>
      <td>
        <div id="iamm_personalised_content"></div>
      </td>
    </tr>

  </table>

</div>

