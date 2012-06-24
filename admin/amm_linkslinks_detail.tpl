{foreach from=$datas.links key=name item=link}
<li class='connectedSortable pluginBox' linkId='{$link.id}'>
  <table>
    <tr>
      <td rowspan=2 style='width:22px'>
        <span class='listMove' title="{'Drag to re-order'|@translate}"></span>
      </td>

      <td rowspan=2 style='width:30px;'>{if $link.icon!=""}<img src='{$link.icon}'/>{else}&nbsp;{/if}</td>
      <td style='width:50%;'>{$link.label}</td>


      <td>{'g002_mode'|@translate}&nbsp;:&nbsp;{$link.mode}</td>


      <td rowspan=2 width="40px">
        <span class='buttonEdit'
              title="{'g002_edit'|@translate}"
              onclick='ulm.edit({$link.id});'/>

        <span class='buttonDelete'
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

