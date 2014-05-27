<div class="banner">
	<h{$card->recursive_level+1}>
	<a href="{$current_app_virtual_url}{$card->name}" class="white_text {if !$card->exists}striked light{/if}" title="{$card->display_name}">
		{$card->display_name}
	</a>
	</h{$card->recursive_level+1}>
	<div class="right align_right">
		{if !$card->exists}
			<a class="lighter_text" href="{$current_app_virtual_url}{$card->name}/edit" title="create">
				<span class="favorite lighter_text fa fa-pencil fa-fw" data-card-name="{$card->name}"></span>
			</a>
		{else}
			{if !$card->is_recursive}
				<span class="white_text">{$card->last_edit}</span><br>
			{/if}
			<span class="favorite lighter_text fa fa-star-o fa-fw" data-card-name="{$card->name}" title="add in favorite"></span>&nbsp;
			{if !$logged and $card->is_private}
				<span class="lighter_text">PRIVATE</span>
			{else}
				<a class="right lighter_text" href="{$current_app_virtual_url}{$card->name}/edit" title="edit">
					<span class="favorite lighter_text fa fa-pencil fa-fw" data-card-name="{$card->name}"></span>
				</a>
			{/if}
		{/if}
	</div>
	<div class="clearer"></div>
</div>