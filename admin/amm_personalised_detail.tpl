
<table class="littlefont">
  {foreach from=$datas.blocks key=name item=block}
  <tr>
    <td>{$block.nfo}</td>
    <td style='width:40%;'>{$block.title}</td>
    <td style='width:80px;'>{$block.visible}</td>

    <td width="40px">
      <span class="buttonEdit"
           title="{'g002_edit'|@translate}"
           onclick='upbm.edit({$block.id});'/>
      <span class="buttonDelete"
            title="{'g002_delete'|@translate}"
            onclick='upbm.remove({$block.id});'/>
    </td>
  </tr>
  {/foreach}
</table>


