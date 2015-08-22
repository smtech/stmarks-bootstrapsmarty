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
