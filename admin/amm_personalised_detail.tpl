
<table class="littlefont">
  {foreach from=$datas.blocks key=name item=block}
  <tr>
    <td>{$block.nfo}</td>
    <td style='width:40%;'>{$block.title}</td>
    <td style='width:80px;'>{$block.visible}</td>

    <td width="40px">
      <img src="{$themeconf.admin_icon_dir}/edit_s.png"
           class="button" alt="{'g002_edit'|@translate}"
           title="{'g002_edit'|@translate}"
           onclick='upbm.edit({$block.id});'/>
      <img src="{$themeconf.admin_icon_dir}/delete.png"
           class="button"
           alt="{'g002_delete'|@translate}"
           title="{'g002_delete'|@translate}"
           onclick='upbm.remove({$block.id});'/>
    </td>
  </tr>
  {/foreach}
</table>


