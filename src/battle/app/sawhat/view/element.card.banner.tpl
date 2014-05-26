<div class="banner">
	<span class="big favorite lighter_text fa fa-star-o" data-card-name="{$card->name}"></span>&nbsp;
	<h{$card->recursive_level+1}>
	<!--{if !$card->is_recursive}
		<a href="{$current_app_virtual_url}" class="lighter_text">
			<b class="bigger">&lsaquo;</b>
		</a>
	{/if}-->
	<a href="{$current_app_virtual_url}{$card->name}" class="white_text {if !$card->exists}striked light{/if}" title="{$card->display_name}">
		{$card->display_name}
	</a>
	</h{$card->recursive_level+1}>
	<div class="right align_right">
		{if !$card->exists}
			<a class="lighter_text" href="{$current_app_virtual_url}{$card->name}/edit">CREATE</a>
		{else}
			{if !$card->is_recursive}
				<span class="white_text">{$card->last_edit}</span>
			{/if}
			
			{if !$logged and $card->is_private}
				<br><span class="lighter_text">PRIVATE</span>
			{else}
				<br><a class="right lighter_text" href="{$current_app_virtual_url}{$card->name}/edit">EDIT</a>
			{/if}
		{/if}
	</div>
	<div class="clearer"></div>
</div>