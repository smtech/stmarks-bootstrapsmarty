{config_load file="stmarkssmarty.conf"}
<!DOCTYPE html>
<html>
	<head>
		{block name="bootstrap-meta"}
			<meta charset="utf-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="viewport" content="width=device-width, initial-scale=1" />
		{/block}
	
		<title>{$metadata['APP_NAME']} &mdash; St. Mark&rsquo;s School</title>
		
		{block name="stylesheet"}
			{block name="bootstrap-stylesheet"}
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" />
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
			{/block}
			
			{block name="custom-stylesheet"}
				{foreach $uiStylesheets as $name => $stylesheet}
					<link rel="stylesheet" href="{$stylesheet}{if $name == 'StMarksSmarty'}?isFramed={if $isFramed}true{else}false{/if}{/if}" {if !empty($name) && !is_int($name)}name="{$name}"{/if} type="text/css" />
				{/foreach}
			{/block}
		{/block}
	</head>
	<body>
	
		{block name="header"}
			{include file="header.tpl"}
		{/block}
		
		{block name="messages"}
			{include file="messages.tpl"}
		{/block}
		
		{block name="content"}
			<div class="container">
				{$content|default:"No content."}
			</div>
		{/block}
		
		{block name="footer"}
			{include file="footer.tpl"}
		{/block}

		{block name="bootstrap-script"}
			<!-- JQuery and Bootstrap (loaded last to decrease page load time) -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
			<script src="{$metadata['APP_URL']}/vendor/smtech/stmarkssmarty/js/ie10-viewport-bug-workaround.js"></script>
			<script src="{$metadata['APP_URL']}/vendor/smtech/stmarkssmarty/js/button-spinner.js"></script>
			{if !empty($uiStylesheets['eternicode/bootstrap-datepicker'])}
				<script src="{$metadata['APP_URL']}/vendor/smtech/stmarkssmarty/js/bootstrap-datepicker.min.js"></script>
				<script>
					$('.input-group.date').datepicker({
						orientation: 'top auto',
					    autoclose: true,
					    todayHighlight: true
					});
				</script>
			{/if}
		{/block}
	</body>
</html>