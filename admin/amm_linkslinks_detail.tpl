{foreach from=$datas.links key=name item=link}
<li class='connectedSortable pluginBox' linkId='{$link.id}'>
  <table>
    <tr>
      <td rowspan=2 style='width:22px'><img src="{$themeconf.admin_icon_dir}/cat_move.png" class="drag_button" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}"/></td>

      <td rowspan=2 style='width:30px;'>{if $link.icon!=""}<img src='{$link.icon}'/>{else}&nbsp;{/if}</td>
      <td style='width:50%;'>{$link.label}</td>


      <td>{'g002_mode'|@translate}&nbsp;:&nbsp;{$link.mode}</td>


      <td rowspan=2 width="40px">
        <img src="{$themeconf.admin_icon_dir}/edit_s.png"
             class="button" alt="{'g002_edit'|@translate}"
             title="{'g002_edit'|@translate}"
             onclick='ulm.edit({$link.id});'/>
        <img src="{$themeconf.admin_icon_dir}/delete.png"
             class="button"
             alt="{'g002_delete'|@translate}"
             title="{'g002_delete'|@translate}"
             onclick='ulm.remove({$link.id});'/>
      </td>
    </tr>

    <tr>
      <td>{$link.url}</td>
      <td>{'g002_visible'|@translate}&nbsp;:&nbsp;{$link.visible}</td>
    </tr>

  </table>
</li>
{/foreach}

