<h2 style="float:right;top:-24px;position:relative;height:auto;font-size:12px;font-weight:normal;">{$plugin.AMM_VERSION}</h2>

<div class='helps'>
  {if isset($pageNfo)}
  <p>{$pageNfo}</p>
  {/if}
</div>

<div id='iBDProcessing' style="display:none;position:absolute;left:0;top:0;width:100%;height:100%;background:#000000;opacity:0.75;z-index:800;">
  <img src="plugins/GrumPluginClasses/icons/processing.gif" style="margin-top:20%;">
</div>

<div id='iConfigState' style='display:none;'>
</div>

{$AMM_BODY_PAGE}

