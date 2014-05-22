<footer class="content">
	<div class="left">
		{if $logged}
		<form method="POST">
			<b class="bigger">&rsaquo;&nbsp;</b><button class="btn btn-link" type="submit" name="submit" value="logout">Logout</button> 
		</form>
		{/if}
		<b class="bigger">&rsaquo;&nbsp;</b><a href="{$current_app_virtual_url}all_cards">See all cards</a>
		<br>
		<b class="bigger">&rsaquo;&nbsp;</b><a href="{$current_app_virtual_url}">Home</a>
		<br>
		<b class="bigger">&rsaquo;&nbsp;</b><a id="toggle_width" data-width-mode="stretch">Toggle Width</a>
	</div>
	<form method="POST" class="right">
		<input type="text" class="input-medium" name="search" placeholder="keywords go here" value="" required="required" pattern="[a-zA-Z0-9 _-]+">
		<button class="btn" type="submit" name="submit" value="search">search</button> 
	</form>
	<div class="clearer"></div>
</footer>