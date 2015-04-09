{include file="element.breadcrumbs.tpl"}
<div class="user_utils right">
	{if $batl_is_logged}
	<form method="POST" class="inline">
		<button class="btn btn-link" type="submit" name="submit" value="logout" title="Logout"><span class="fa fa-sign-out fa-fw"></span></button> 
	</form>
	{/if}
	<button id="toggle_width" class="btn btn-link" data-width-mode="stretch" title="Stretch view"><span class="fa fa-expand fa-fw"></span></button>
</div>
