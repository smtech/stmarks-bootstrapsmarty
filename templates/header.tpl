<!DOCTYPE html>
<html>

<head>
	<title>{$metadata['APP_NAME']}</title>
	{foreach $stylesheets as $stylesheet}
		<link rel="stylesheet" href="{$stylesheet}" type="text/css" />
	{/foreach}
</head>

<body>

<header id="header">
	<div id="header-logo"></div>
	{include file="navigation-menu.tpl"}
</header>

{if count($messages) > 0}
<div id="messages">
	<ul>
		{foreach $messages as $message}
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