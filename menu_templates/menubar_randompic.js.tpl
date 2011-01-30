{literal}
<script type="text/javascript">
var randomPictOpt={
    {/literal}
      fixedHeight:{$data.blockHeight},
      delay:{$data.delay},
      showName:"{$data.showname}",
      showComment:"{$data.showcomment}",
      pictures:[
        {foreach from=$data.pictures item=picture name=items}
          {ldelim}
            'comment':'{$picture.comment|escape:'quotes'|replace:"\n":'<br>'|replace:"\r":''}',
            'link':"{$picture.link}",
            'name':'{$picture.imgname|escape:'quotes'|replace:"\n":'<br>'|replace:"\r":''}',
            'thumb':"{$picture.thumb}"
          {rdelim}
          {if !$smarty.foreach.items.last},{/if}
        {/foreach}
      ]
    {literal}
    };
</script>
{/literal}
