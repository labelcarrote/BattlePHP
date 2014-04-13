{if isset($cards)}
<div class="content line">
	{foreach from=$cards item=card}
	<section class="unit size1of3">
		<div class="smallermargin" id="{$card->name}">
			<style>{$card->style_definition}</style>
			{include file="element.card.banner.tpl"}
		</div>
	</section>
	{/foreach}
</div>
{elseif isset($card)}
<div class="content">
	<section class="size1of1">
	{include file="element.card.v2.tpl"}
	</section>
</div>
{else}
<div class="content">
	<section class="size1of1">
	No card were found.
	</section>
</div>
{/if}
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
		<b class="bigger">&rsaquo;&nbsp;</b><a id="toggle_width" href="#" data-width-mode="stretch">Toggle Width</a>
	</div>
	<form method="POST" class="right">
		<input type="text" class="input-medium" name="search" placeholder="search one word only" value="" required="required" pattern="[a-zA-Z0-9\-]+">
		<button class="btn" type="submit" name="submit" value="search">search</button> 
	</form>
	<div class="clearer"></div>
</footer>