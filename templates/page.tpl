{config_load file="stmarkssmarty.conf"}

<div id="wrapper">
	{include file="header.tpl"}
	
	<div id="content">
		{block name="content"}{$content|default:"No content."}{/block}
	</div>
	
	{include file="footer.tpl"}
</div>