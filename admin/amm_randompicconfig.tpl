{known_script id="jquery.ui" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.core.packed.js"}
{known_script id="jquery.ui.slider" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.slider.packed.js"}

{literal}
<style>
 .ui-slider {
    width:350px;
    height:10px;
    border:1px solid;
    margin-right:5px;
    padding-right:14px;
  }
 .ui-slider-handle {
    width:12px;
    height:12px;
    position:relative;
    top:-2px;
    border:1px solid;
    display:block;
  }
</style>
<script type="text/javascript">

  //global var ; need to not have to initialize them every time a value is changed
  var objlang;
  var objnames = new Array('iamm_randompicture_title');
  var objinput = new Array();   //input text from form => objinput[name]
  var objhidden = new Array();  //input hidden from form => objhidden[name][lang]

  function init()
  {
    objlang = document.getElementById('islang');
    for(i=0;i<objnames.length;i++)
    {
      objinput[i] = document.getElementById(objnames[i]);
      objhidden[i] = new Array();
      for(j=0;j<objlang.options.length;j++)
      {
        objhidden[i][j] = document.getElementById(objnames[i]+'_'+objlang.options[j].value);
      }
    }

    formatDelay({/literal}{$datas.periodic_change}{literal});
    $("#iamm_rp_pc_slider").slider(
      {
        min:0,
        max:60000,
        steps:120,
        startValue:{/literal}{$datas.periodic_change}{literal},
        slide: function(event, ui) { formatDelay(ui.value); }
      });

    formatHeight({/literal}{$datas.height}{literal});
    $("#iamm_rp_height_slider").slider(
      {
        min:99,
        max:300,
        steps:201,
        startValue:affectHeight({/literal}{$datas.height}{literal}),
        slide: function(event, ui) { formatHeight(ui.value); }
      });
  }

  function formatDelay(delay)
  {
    $("#iamm_randompicture_periodicchange").val(delay);
    if(delay==0)
    {
      $("#iamm_rp_pc_display").html("{/literal}{'g002_setting_randompic_periodicchange_deactivated'|@translate}{literal}");
    }
    else
    {
      $("#iamm_rp_pc_display").html((delay/1000).toFixed(2)+"s");
    }
  }

  function formatHeight(height)
  {
    (height==99)?vheight=0:vheight=height;

    $("#iamm_randompicture_height").val(vheight);
    if(vheight==0)
    {
      $("#iamm_rp_height_display").html("{/literal}{'g002_setting_randompic_height_auto'|@translate}{literal}");
    }
    else
    {
      $("#iamm_rp_height_display").html(vheight+"px");
    }
  }

  function affectHeight(height)
  {
    if(height==0)
    {return(0);}else{return(height);}
  }

  function change_lang()
  {
    for(i=0;i<objnames.length;i++)
    {
      objinput[i].value = objhidden[i][objlang.options.selectedIndex].value;
    }
  }

  function apply_changes(input_id)
  {
    var obj=document.getElementById(input_id);
    objhidden[objnames.indexOf(input_id)][objlang.options.selectedIndex].value = obj.value;
  }

  function do_translation()
  {
    var inputid = document.getElementById('iamm_randompicture_title');
    var tolang = objlang.options[objlang.options.selectedIndex].value.substr(0,2);

    google_translate(inputid.value, '{/literal}{$datas.fromlang}{literal}', tolang, inputid, 'value', apply_changes, inputid.id);

  }


</script>
{/literal}



<h3>{'g002_configrandompic'|@translate}</h3>


<form method="post" action="" class="general">
  <fieldset>
    <legend>{'g002_setting_block_menu'|@translate}</legend>

    {if isset($datas.language_list) and count($datas.language_list)}
      {foreach from=$datas.language_list key=name item=language_row}
        <input type="hidden" name="famm_randompicture_title_{$language_row.LANG}"
                id="iamm_randompicture_title_{$language_row.LANG}" value="{$language_row.MENUBARTIT}">
      {/foreach}
    {/if}

    <table class="formtable">
      <tr>
        <td>{'g002_setting_block_title'|@translate}</td>
        <td colspan="2">
          <input type="text" id="iamm_randompicture_title" value="" maxlength="50" onkeyup="apply_changes('iamm_randompicture_title');" onblur="apply_changes('iamm_randompicture_title');"/>
          <select onchange="change_lang();" id="islang">
            {html_options values=$datas.language_list_values output=$datas.language_list_labels selected=$datas.lang_selected}
          </select><br>
        </td>
      </tr>
      <tr>
        <td></td>
        <td style="font-size:80%;" colspan="2">
          <a style="cursor:pointer;" onclick="do_translation()">{'g002_translate'|@translate}</a>
        </td>
      </tr>

      <tr>
        <td>{'g002_setting_randompic_height'|@translate}</td>
        <td>
          <input type="hidden" name="famm_randompicture_height" id="iamm_randompicture_height" value="{$datas.height}">
          <div id="iamm_rp_height_slider"></div>
        </td>
        <td width="90px">
          <div id="iamm_rp_height_display"></div>
        </td>
      </tr>


    </table>

  </fieldset>

  <fieldset>
    <legend>{'g002_setting_randompic_aboutpicture'|@translate}</legend>
      <table class="formclass">
        <tr>
          <td>{'g002_setting_randompic_showname'|@translate}</td>
          <td>
            <select name="famm_randompicture_showname" id="iamm_randompicture_showname">
              {html_options values=$datas.show_values output=$datas.show_labels selected=$datas.showname_selected}
            </select>
          </td>
        </tr>

        <tr>
          <td>{'g002_setting_randompic_showcomment'|@translate}</td>
          <td>
            <select name="famm_randompicture_showcomment" id="iamm_randompicture_showcomment">
              {html_options values=$datas.show_values output=$datas.show_labels selected=$datas.showcomment_selected}
            </select>
          </td>
        </tr>

      </table>
  </fieldset>


  <fieldset>
    <legend>{'g002_setting_randompic_periodicchange'|@translate}</legend>
      <table class="formclass">
        <tr>
          <td>{'g002_setting_randompic_periodicchange_delay'|@translate}</td>
          <td>
            <input type="hidden" name="famm_randompicture_periodicchange" id="iamm_randompicture_periodicchange" value="{$datas.periodic_change}">
            <div id="iamm_rp_pc_slider"></div>
          </td>
          <td width="70px">
            <div id="iamm_rp_pc_display"></div>
          </td>
        </tr>


      </table>
  </fieldset>

  <p>
    <input type="submit" name="famm_submit_apply" id="iamm_submit_apply" value="{'g002_apply'|@translate}" >
  </p>

  <input type="hidden" name="famm_modeedit" value="config">

</form>

<script type="text/javascript">
  init();
  change_lang();
</script>
