{known_script id="jquery" src=$ROOT_URL|cat:"template-common/lib/jquery.packed.js"}

<!-- random picture menu bar -->
<dt>{$block->get_title()}</dt>

{literal}
<script type="text/javascript">
//var divnum = 1;
//var divprec = 0;

  function init()
  {
    $("#iamm_ill0").hide();
    //$("#iamm_ill1").hide();
  }

  function getRandomPicture()
  {
    //divprec=divnum;
    //divnum=1-divnum;
    $.get("./index.php", {ajaxfct:"randompic"},
      function (data)
      {
        $("#iamm_ill0").fadeTo('slow', 0, function ()
        {
          $("#iamm_ill0").html(data);
          $("#iammrpic").load( function () {
            $("#irandompicdd").animate({height: ($("#iamm_ill0").height())+"px"}, "normal", function ()
            {
              $("#iamm_ill0").fadeTo('slow', 1, function ()
              {
                $("#irandompicdd").animate({height: this.clientHeight+"px"}, "normal");
              });
            });
          } );
        } );
      }
    );
  }
</script>
{/literal}

<dd id="irandompicdd" class="randompicdd">
  <div id="iamm_ill0" class="illustration ammillustration"></div>
  <!--<div id="iamm_ill1" class="illustration ammillustration"></div>-->
</dd>

<script type="text/javascript">
  getRandomPicture();
  {if $block->data.delay > 0 }
    var vIntervalID = window.setInterval(getRandomPicture, {$block->data.delay});
  {/if}
</script>
