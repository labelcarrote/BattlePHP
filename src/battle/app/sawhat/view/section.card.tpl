{************************************************

 Card Section 
 
 in :
 - $card : Card
 - $cards : [Card]
 
************************************************}
<div class="content content_theme_sawhat">
	{include file="element.header.tpl"}
	<section>
	{if isset($cards)}
		<div class="sawhat card__content">
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
	{elseif isset($card)}
		{include file="element.card.tpl"}
	{else}
		<div class="sawhat card__content">
		No card were found.
		</div>
	{/if}
	</section>
</div>
{include file="element.footer.tpl"}