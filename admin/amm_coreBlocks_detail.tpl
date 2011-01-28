<h3>{'User'|@translate}</h3>
<p>
{foreach from=$users item=values}
  <label><input type="checkbox" value="{$values.id}" class='visibilityUser' {if $values.allowed==true} checked{/if} />&nbsp;{$values.name}</label><br/>
{/foreach}
</p>

<h3>{'Group'|@translate}</h3>
<p>
{foreach from=$groups item=values}
  <label><input type="checkbox" value="{$values.id}" class='visibilityGroup' {if $values.allowed==true} checked{/if} />&nbsp;{$values.name}</label><br/>
{/foreach}
</p>
