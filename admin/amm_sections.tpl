{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js"}
{known_script id="jquery.ui.sortable" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.sortable.packed.js"}

<div id="containerMenu">
  {foreach from=$sections item=section}
  <div id="containerMenu_{$section}" class="containerMenuSection">
    <h3>{$section}</h3>
    <ul class="connectedSortable categoryUl" id="menu_{$section}">
    {foreach from=$items item=data key=id}
      {if $data.container==$section}
        <li class="categoryLi menuItem {if $id=='qsearch'}menuItemDisabled{/if}" id="i{$id}">
          <img src="{$themeconf.admin_icon_dir}/cat_move.png" class="button drag_button" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}"/>
          {$data.translation|@translate}
        </li>
      {/if}
    {/foreach}
    </ul>
  </div>
  {/foreach}

  <input type="button" value="{'cancel'|@translate}" onclick="resetMenu('cancel');"/>
  <input type="button" value="{'piwigo_default'|@translate}" onclick="resetMenu('default');"/>
  <p><input type="button" value="{'apply_changes'|@translate}" onclick="submitChanges();"></p>
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
    resetValues[0].push( {literal} { {/literal} id:"{$id}", section:"{$value.container}", order:{$value.order}  {literal} } {/literal} );
  {/foreach}
  {foreach from=$items item=value key=id}
    resetValues[1].push( {literal} { {/literal} id:"{$id}", section:"{$value.container}", order:{$value.order}  {literal} } {/literal} );
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
    }
  }

  function submitChanges()
  {
    datas="";

    items=$("#menu_menu").children();
    for(i=0;i<items.length;i++)
    {
      datas+=items.get(i).id.substr(1)+",menu,"+i+",();";
    }

    items=$("#menu_special").children();
    for(i=0;i<items.length;i++)
    {
      datas+=items.get(i).id.substr(1)+",special,"+i+",();";
    }

    $("#iList").val(datas);
    $("#submitForm").get(0).submit();
  }

</script>
{/literal}
