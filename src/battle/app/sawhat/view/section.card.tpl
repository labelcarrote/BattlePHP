{if isset($cards)}

<div class="content">
	{include file="element.header.tpl"}
	<section class="size1of1">
		<div class="sawhat things">
		<!-- <div class="starred_title smaller">
			<span class="fa-stack">
				<span class="lighter_text fa fa-circle-thin fa-stack-2x"></span>
				<span class="fa fa-star fa-stack-1x"></span>
			</span>
		</div> -->

		<div class="all_cards_container auto_clear">
			{foreach from=$cards item=card}
			<section class="unit size1of3">
				<div class="smallermargin" id="{$card->name}">
					<style>{$card->style_definition}</style>
					{include file="element.card.loadable.tpl"}
				</div>
			</section>
			{/foreach}
			</div>
		</div>
	</section>
</div>

{elseif isset($card)}
<div class="content">
	{include file="element.header.tpl"}
	<section class="size1of1">
	{include file="element.card.tpl"}
	</section>
</div>
{else}
<div class="content">
	{include file="element.header.tpl"}
	<section class="size1of1">
	<div class="sawhat things">
	No card were found.
	</div>
	</section>
</div>
{/if}
{include file="element.footer.tpl"}