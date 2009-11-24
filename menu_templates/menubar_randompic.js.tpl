{literal}
<script type="text/javascript">
var fixedHeight = {/literal}{$data.blockHeight}{literal};
var vIntervalID;

  $(document).ready(
    function ()
    {
      {/literal}
      {if $data.blockHeight>0}
        $("#irandompicinner").height(fixedHeight);
        {literal}$("#iammrpic").load( function () { computePositionTop(); } );{/literal}
      {else}
        {literal}
        $("#iammrpic").load( function () { $("#irandompicinner").animate({height: ($("#iamm_ill0").innerHeight())+"px"}, "normal"); } );
        {/literal}
      {/if}
      {if $data.delay > 0 }
        vIntervalID = window.setInterval(getRandomPicture, {$data.delay});
      {/if}
      {literal}
    }
  );

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
          {if $data.blockHeight>0}
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

  {/literal}

  {literal}


</script>
{/literal}
