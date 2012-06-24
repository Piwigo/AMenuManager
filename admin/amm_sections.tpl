{combine_script id="jquery.ui" require='jquery' path="themes/default/js/ui/jquery.ui.core.js"}
{combine_script id="jquery.ui.widget" require='jquery.ui' path="themes/default/js/ui/jquery.ui.widget.js"}
{combine_script id="jquery.ui.mouse" require='jquery.ui.widget' path="themes/default/js/ui/jquery.ui.mouse.js"}
{combine_script id="jquery.ui.position" require='jquery.ui.widget' path="themes/default/js/ui/jquery.ui.position.js"}


<div id="containerMenu">
  {foreach from=$sections item=sectionName key=section}
  <div id="containerMenu_{$section}" class="containerMenuSection">
    <h3>{$sectionName|@translate}</h3>
    <ul class="connectedSortable categoryUl" id="menu_{$section}">
    {foreach from=$items item=data key=id}
      {if $data.container==$section}
        <li class="categoryLi menuItem {if $id=='qsearch'}menuItemDisabled{/if}" id="i{$id}">
          {if $id=="qsearch"}
          {else}
          <span class='listMove' title="{'Drag to re-order'|@translate}"></span>
          {/if}
          {$data.translation|@translate}
          <a  onclick="switchVisibility('i{$id}_visibility');">
          <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/permissions.png"  class="button drag_button" alt="{'g002_click_to_manage_rights'|@translate}" title="{'g002_click_to_manage_rights'|@translate}"
                style="float:right;"/></a>
          <div id="i{$id}_visibility" class="visibility">
            {$data.visibilityForm}
          </div>
        </li>
      {/if}
    {/foreach}
    </ul>
  </div>
  {/foreach}
  <br/>
  <input type="button" value="{'g002_cancel'|@translate}" onclick="resetMenu('cancel');"/>
  <input type="button" value="{'g002_piwigo_default'|@translate}" onclick="resetMenu('default');"/>
  <p><input type="button" value="{'g002_apply_changes'|@translate}" onclick="submitChanges();"></p>
  <form id="submitForm" method="POST" action="">
    <input type="hidden" name="fList" id="iList" value=""/>
  </form>
</div>


{literal}
<script type="text/javascript">

  var resetValues = new Array(new Array(), new Array());

  {/literal}

  // initialization for cancel et reset functions
  {foreach from=$defaultValues item=value key=id}
    resetValues[0].push( {literal} { {/literal} id:"{$id}", section:"{$value.container}", order:{$value.order}, visibility:"{$value.visibility}" {literal} } {/literal} );
  {/foreach}
  {foreach from=$items item=value key=id}
    resetValues[1].push( {literal} { {/literal} id:"{$id}", section:"{$value.container}", order:{$value.order}, visibility:"{$value.visibility}" {literal} } {/literal} );
  {/foreach}
  {literal}

  $("#containerMenu").sortable(
    {
      connectWith: '.connectedSortable',
      cursor: 'move',
      opacity:0.6,
      items: 'li:not(.menuItemDisabled)',
      tolerance:'pointer'
    }
  );

  function resetMenu(mode)
  {
    (mode=='default')?key=0:key=1;

    for(i=0;i<resetValues[key].length;i++)
    {
      $("#menu_"+resetValues[key][i].section).get(0).appendChild($("#i"+resetValues[key][i].id).get(0));

      {/literal}
      {foreach from=$visibility.users item=value}
      $("#i"+resetValues[key][i].id+"_vis_user_{$value.id}").get(0).checked=/(.*,|^){$value.id}(?!\w)(\/)?/.test(resetValues[key][i].visibility);
      {/foreach}

      {foreach from=$visibility.groups item=value}
      $("#i"+resetValues[key][i].id+"_vis_group_{$value.id}").get(0).checked=/(\/.*,|\/){$value.id}(?!\w)(\/)?/.test(resetValues[key][i].visibility);
      {/foreach}
      {literal}

    }
  }

  function submitChanges()
  {
    datas="";

    items=$("#menu_menu").children();
    for(i=0;i<items.length;i++)
    {
      datas+=items.get(i).id.substr(1)+",menu,"+i+"#"+makeVisibility(items.get(i).id)+";";
    }

    items=$("#menu_special").children();
    for(i=0;i<items.length;i++)
    {
      datas+=items.get(i).id.substr(1)+",special,"+i+"#"+makeVisibility(items.get(i).id)+";";
    }

    $("#iList").val(datas);
    $("#submitForm").get(0).submit();
  }

  function switchVisibility(id)
  {
    if($("#"+id).css('display')!='none')
    {
      $("#"+id).css({display:'none'});
    }
    else
    {
      $("#"+id).css({display:'block'});
    }
  }

  function makeVisibility(id)
  {
    {/literal}
    returned="";
    {foreach from=$visibility.users item=value}
    returned+=($("#"+id+"_vis_user_{$value.id}").get(0).checked)?((returned=="")?"":",")+"{$value.id}":"";
    {/foreach}

    returned2="";
    {foreach from=$visibility.groups item=value}
    returned2+=($("#"+id+"_vis_group_{$value.id}").get(0).checked)?((returned2=="")?"":",")+"{$value.id}":"";
    {/foreach}
    {literal}

    return(returned+"/"+returned2);
  }

</script>
{/literal}
