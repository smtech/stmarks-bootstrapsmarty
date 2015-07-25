<!DOCTYPE html>
<html>

<head>
	<title>{$metadata['APP_NAME']}</title>
	{foreach $uiStylesheets as $name => $stylesheet}
		<link rel="stylesheet" href="{$stylesheet}" {if !empty($name) && !is_int($name)}name="{$name}"{/if} type="text/css" />
	{/foreach}
</head>

<body>

<header id="header">
	<div id="header-logo"></div>
	{include file="navigation-menu.tpl"}
</header>

{if count($uiMessages) > 0}
<div id="messages">
	<ul>
		{foreach $uiMessages as $message}
			<li>
				<div class="message {$message->class|default:"message"}">
					<span class="title">{$message->title}</span><br />
					<span class="content">{$message->content}</span>
				</div>
			</li>
		{/foreach}
	</ul>
</div>
{/if}