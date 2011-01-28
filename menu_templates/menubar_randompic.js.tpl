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
            'comment':"{$picture.comment}",
            'link':"{$picture.link}",
            'name':"{$picture.name}",
            'thumb':"{$picture.thumb}"
          {rdelim}
          {if !$smarty.foreach.items.last},{/if}
        {/foreach}
      ]
    {literal}
    };
</script>
{/literal}
