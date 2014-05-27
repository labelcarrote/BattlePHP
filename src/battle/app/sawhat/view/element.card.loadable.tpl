<div class="loadable_card">
	<div class="banner loadable">
		<a
			href="{$current_app_virtual_url}{$card_name}"
			class="white_text{if $card_exists} striked light{/if}"
			title="{$card_display_name}"
		>
			<span class="fa fa-caret-right"></span>&nbsp;<b>{$card_display_name}</b>
		</a>
		<a
			class="right lighter_text load_card"
			data-action="load"
			data-card-name="{$current_app_virtual_url}{$card_name}"
			title="load"
		>
			<span class="lighter_text fa fa-chevron-circle-down"></span>
		</a>
		<div class="clearer"></div>
	</div>
	<div class="darker include hidden"></div>
</div>