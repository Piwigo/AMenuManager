{if $datas.IMGNAME!="" and $datas.SHOWNAME=="o"}{$datas.IMGNAME}<br/>{/if}
{* No strip_tags because comment could have those for good reasons *}
{* Over comment is limited to 127 characters for look only *}
{if $datas.IMGCOMMENT!="" and $datas.SHOWCOMMENT=="o" and strlen($datas.IMGCOMMENT) < 128}{$datas.IMGCOMMENT}<br/>{/if}
<a href="{$datas.LINK}"><img id="iammrpic" src="{$datas.IMG}"/></a>
{if $datas.IMGNAME!="" and $datas.SHOWNAME=="u"}<br/>{$datas.IMGNAME}{/if}
{* Under comment is limited to 255 characters *}
{if $datas.IMGCOMMENT!="" and $datas.SHOWCOMMENT=="u" and strlen($datas.IMGCOMMENT) < 256}<br/>{$datas.IMGCOMMENT}{/if}
