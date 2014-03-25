<div class="banner" {if $card->exists}style="background-color:{$card->color}"{/if}>
	<h{$card->recursive_level+1}>
	{if !$card->is_recursive}
		<a href="{$current_app_virtual_url}" class="lighter_text">
			<b class="bigger">&lsaquo;</b>
		</a>
	{/if}
	<a href="{$current_app_virtual_url}{$card->name}" class="white_text {if !$card->exists}striked light{/if}">
		{$card->display_name}
	</a>
	</h{$card->recursive_level+1}>
	{if !$logged and $card->is_private}
		<div class="right align_right "><span class="lighter_text">PRIVATE</span></div>
	{elseif !$card->exists}
		<div class="right align_right">
			<a class="lighter_text" href="{$current_app_virtual_url}{$card->name}/edit">CREATE</a>
		</div>
	{elseif $card->is_recursive}
		<div class="right align_right">
			<span class="white_text">{$card->last_edit}</span><br>
			<a class="lighter_text" href="{$current_app_virtual_url}{$card->name}/edit">EDIT</a>
		</div>
	{else}
		<a class="right lighter_text" href="{$current_app_virtual_url}{$card->name}/edit">EDIT</a>
	{/if}
	<br class="clearer">
</div>