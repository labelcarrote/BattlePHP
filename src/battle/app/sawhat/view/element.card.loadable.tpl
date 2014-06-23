<div class="loadable_card">
	<div class="banner loadable auto_clear" {if $card_exists}style="background-color:{$card->color};"{/if}>
		<a
			href="{$current_app_virtual_url}{$card_name}"
			class="{if !$card_exists || $card->is_light}white_text{else}black_text{/if}{if !$card_exists} striked light{/if}"
			title="{$card_display_name}"
		>
			<span class="fa fa-caret-right"></span>&nbsp;<b>{$card_display_name}</b>
		</a>
		<a
			class="right {if !$card_exists || $card->is_light}lighter_text{else}darker_text{/if} load_card"
			data-action="load"
			data-card-name="{$current_app_virtual_url}{$card_name}"
			title="load"
		>
			<span class="fa fa-chevron-circle-down"></span>
		</a>
	</div>
	<div class="darker include hidden"></div>
</div>