{config_load file="stmarkssmarty.conf"}
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
	
		<title>{$metadata['APP_NAME']} &mdash; St. Mark&rsquo;s School</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" />
		
		{foreach $uiStylesheets as $name => $stylesheet}
			<link rel="stylesheet" href="{$stylesheet}" {if !empty($name) && !is_int($name)}name="{$name}"{/if} type="text/css" />
		{/foreach}
	</head>
	<body>
	
		{include file="header.tpl"}
		
		{block name="ui-messages"}
			{if count($uiMessages) > 0}
				<div id="messages" class="container">
					{foreach $uiMessages as $message}
						<div class="alert alert-dismissable {$message->class|default:"alert-info"}">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<strong>{$message->title}</strong> {$message->content}
						</div>
					{/foreach}
				</div>
				{/if}
		{/block}
		
		{block name="content"}
			<div class="container">
				{$content|default:"No content."}
			</div>
		{/block}
		
		{include file="footer.tpl"}
	
		<!-- JQuery and Bootstrap (loaded last to decrease page load time) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="{$metadata['APP_URL']}/vendor/smtech/stmarkssmarty/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>