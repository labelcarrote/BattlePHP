{************************************************

 Loadable Card Element (small banner?)
 
 in :
 - $card : Card
 
************************************************}
<div class="loadable_card">
	<div class="banner loadable auto_clear" {if $card->exists}style="background-color:{$card->color};"{/if}>
		<a href="{$batl_current_app_virtual_url}{$card->name}"
			class="{if !$card->exists || $card->is_light}white_text{else}black_text{/if}{if !$card->exists} striked light{/if}"
			title="{$card->display_name}">
			<span class="fa fa-caret-right"></span>&nbsp;<b>{$card->display_name}</b>
		</a>
		<a class="right {if !$card->exists || $card->is_light}lighter_text{else}darker_text{/if} load_card"
			data-action="load"
			data-card-name="{$card->name}"
			data-card-url="{$batl_current_app_virtual_url}api/?m=get_card&amp;name={$card->name}"
			title="load">
			<span class="fa fa-chevron-circle-down"></span>
		</a>
		<div class="right">
			&nbsp;
			<span class="starred {if $card->is_light}lighter_text{else}darker_text{/if} fa fa-star-o" data-card-name="{$card->name}" title="add in starred">
			</span>
			&nbsp;
		</div>
		{if (isset($batl_is_logged) && $batl_is_logged) || !$card->is_private}
			<a class="right {if $card->is_light}lighter_text{else}darker_text{/if} no-print" href="{$batl_current_app_virtual_url}{$card->name}/edit" title="edit">
				<span class="{if $card->is_light}lighter_text{else}darker_text{/if} fa fa-pencil" data-card-name="{$card->name}"></span>
			</a>
		{/if}
	</div>
	<div class="darker include hidden"></div>
</div>