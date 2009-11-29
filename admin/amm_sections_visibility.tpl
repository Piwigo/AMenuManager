<h3>{'user'|@translate}</h3>
<p>
{foreach from=$users item=values}
  <label>
  <input type="checkbox" id="i{$name}_vis_user_{$values.id}" value="{$values.id}"
    {if $values.allowed==true} checked{/if} />{$values.name}
  </label><br/>
{/foreach}
</p>

<h3>{'Group'|@translate}</h3>
<p>
{foreach from=$groups item=values}
  <label>
  <input type="checkbox" id="i{$name}_vis_group_{$values.id}" value="{$values.id}"
    {if $values.allowed==true} checked{/if} />{$values.name}
  </label><br/>
{/foreach}
</p>
