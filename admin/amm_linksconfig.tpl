{literal}
<script type="text/javascript">
  var ulc;

  $(window).load(
    function ()
    {
      ulc=new userLinksConfig(
        {}, {},
        '{/literal}{$token}{literal}',
        {
          'showIcons':'{/literal}{$datas.config.showIcons}{literal}',
          'userLang':'{/literal}{$datas.selectedLang}{literal}',
          'titles':
            {
            {/literal}
            {foreach from=$datas.config.titles name=items key=langCode item=title}
              "{$langCode}":"{$title}"{if !$smarty.foreach.items.last},{/if}
            {/foreach}
            {literal}
            },
          'langs':[
              {/literal}
              {foreach from=$datas.config.titles name=items key=langCode item=title}
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

<form method="post" action="" class="general">

  <fieldset>
    <legend>{'g002_setting_block_menu'|@translate}</legend>

    <table class="formtable">
      <tr>
        <td>{'g002_setting_block_title'|@translate}</td>
        <td>
          <div id="iamm_links_title"></div>
          <div id="islang">
            [
            {foreach from=$datas.langs key=langCode item=langLabel name=items}
              {ldelim}"value":"{$langCode}","cols":["{$langLabel}"]{rdelim}{if !$smarty.foreach.items.last},{/if}
            {/foreach}
            ]
          </div>
        </td>
      </tr>

    </table>
  </fieldset>

  <fieldset>
    <legend>{'g002_setting_link_links'|@translate}</legend>
    <table class="formtable">
      <tr>
        <td>{'g002_setting_link_show_icon'|@translate}</td>
        <td>
          <div id='iamm_links_show_icons'>
            <label><input type="radio" value="y">&nbsp;{'g002_yesno_y'|@translate}<br></label>
            <label><input type="radio" value="n">&nbsp;{'g002_yesno_n'|@translate}</label>
          </div>
        </td>
      </tr>
    </table>
  </fieldset>

  <p>
    <input type="button" id="iamm_submit_apply" value="{'g002_apply'|@translate}" onclick="ulc.submit();">
  </p>

</form>

