{assign var="uiMessages" value=$uiMessages|default: null}
{block name="ui-messages"}
	{if !empty($uiMessages)}
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
