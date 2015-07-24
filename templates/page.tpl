{config_load file="stmarkssmarty.conf"}
{include file="header.tpl"}

<div id="content">
	{block name="content"}{$content|default:"No content."}{/block}
</div>

{include file="footer.tpl"}