{known_script id="jquery" src=$ROOT_URL|cat:"template-common/lib/jquery.packed.js"}

<!-- random picture menu bar -->
<dt>{$block->get_title()}</dt>

{literal}
<script type="text/javascript">
var fixedHeight = {/literal}{$block->data.blockHeight}{literal};

  function init()
  {
    {/literal}
    {if $block->data.blockHeight>0}
      $("#irandompicinner").height(fixedHeight);
      {literal}$("#iammrpic").load( function () { computePositionTop(); } );{/literal}
    {else}
      {literal}
      $("#iammrpic").load( function () { $("#irandompicinner").animate({height: ($("#iamm_ill0").innerHeight())+"px"}, "normal"); } );
      {/literal}
    {/if}
    {literal}
  }

  function computePositionTop()
  {
    $("#iamm_ill0").css({top:(fixedHeight-$("#iamm_ill0").innerHeight())/2});
  }

  function getRandomPicture()
  {
    $.get("./index.php", {ajaxfct:"randompic"},
      function (data)
      {
        $("#iamm_ill0").fadeTo('slow', 0, function ()
        {
          $("#iamm_ill0").html(data);

          {/literal}
          {if $block->data.blockHeight>0}
            {literal}
            $("#iammrpic").load( function () {
              computePositionTop();
              $("#iamm_ill0").fadeTo('slow', 1);
            } );
            {/literal}
          {else}
            {literal}
            $("#iammrpic").load( function () {
              $("#irandompicinner").animate({height: ($("#iamm_ill0").innerHeight())+"px"}, "normal", function ()
              {
                $("#iamm_ill0").fadeTo('slow', 1, function ()
                {
                  $("#irandompicinner").animate({height: this.clientHeight+"px"}, "normal");
                });
              });
            } );
            {/literal}
          {/if}
          {literal}

        } );
      }
    );
  }


</script>
{/literal}

<dd id="irandompicdd" class="randompicdd">
  <div id="irandompicinner" class="illustration">
    <div class="ammillustrationc">
      <div id="iamm_ill0" class="ammillustration">{$block->data.firstPicture}</div>
    </div>
  </div>
</dd>

<script type="text/javascript">
  init();
  {if $block->data.delay > 0 }
    var vIntervalID = window.setInterval(getRandomPicture, {$block->data.delay});
  {/if}
</script>
