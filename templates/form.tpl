<div class="container page-header">
	<h1>{$formName}</h1>
</div>
<div class="container">
	<form action="{$formAction}" method="post" class="form-horizontal" role="form">
	{block name="form"}
		<div class="form-group">
			<label for="input1" class="control-label col-sm-2">Input 1</label>
			<div class="col-sm-10">
				<input name="input1" id="input1" type="text" class="form-control" placeholder="Write something" autofocus="autofocus" />
			</div>
		</div>
		<div class="form-group">
			<label for="input2" class="control-label col-sm-2">Input 2</label>
			<div class="col-sm-10">
				<input name="input2" id="input2" type="text" class="form-control" placeholder="Write something else" autofocus="autofocus" />
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Submit</button>
			</div>	
		</div>
	{/block}
	</form>
</div>