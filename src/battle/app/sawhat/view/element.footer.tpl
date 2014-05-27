<footer class="content auto_clear">
	<div class="left">
		{if $logged}
		<form method="POST">
			<span class="fa fa-caret-right"></span>&nbsp;<button class="btn btn-link" type="submit" name="submit" value="logout">Logout</button> 
		</form>
		{/if}
		<span class="fa fa-caret-right"></span>&nbsp;<a href="{$current_app_virtual_url}all_cards">See all cards</a>
		<br>
		<span class="fa fa-caret-right"></span>&nbsp;<a href="{$current_app_virtual_url}help">Help</a>
		<br>
		<span class="fa fa-caret-right"></span>&nbsp;<a id="toggle_width" data-width-mode="stretch">Toggle Width</a>
	</div>
	<form method="POST" class="right">
		<input type="text" class="input-medium" name="search" placeholder="keywords go here" value="" required="required" pattern="[a-zA-Z0-9 _-]+">
		<button class="btn" type="submit" name="submit" value="search" title="search"><span class="fa fa-search"></span></button> 
	</form>
</footer>