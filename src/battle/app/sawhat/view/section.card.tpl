{if isset($cards)}
<div class="content line">
	{foreach from=$cards item=card}
	<section class="unit size1of3">
		<div class="smallermargin">
			{include file="element.card.v2.tpl"}
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
{/if}
<div class="content">
	<div class="left">
		<a href="{$current_app_virtual_url}all_cards">all cards</a>
	</div>

	{if $logged}
	<form class="right" method="POST">
		<button class="btn btn-link" type="submit" name="submit" value="logout">Logout</button> 
	</form>
	{/if}
</div>