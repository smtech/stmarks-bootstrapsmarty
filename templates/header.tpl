{assign var="isFramed" value=$isFramed|default: false}
<header>
	<nav class="navbar {if $isFramed}navbar-default{else}navbar-inverse{/if} navbar-fixed-top" id="header">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a id="header-logo" class="navbar-brand">
					<span class="sr-only">St. Mark&rsquo;s School<span>
				</a>
			</div>
			<div id="navigation-menu" class="collapse navbar-collapse">
				{include file="navigation-menu.tpl"}
			</div>
		</div>
	</nav>
</header>