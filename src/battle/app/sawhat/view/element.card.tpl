<div class="sawhat margin" >
	{if !isset($card)}
		<div class="banner">
			<div class="left">
				<a href="{$current_app_virtual_url}">
					<h1 style="">404</h1>
				</a>
			</div>
			<div class="clear"></div>
		</div>
	{else}
		<div class="banner">
			<div class="left">
				<h1 style="color:{$card->color}">
				{if $card->is_recursive}
					<a href="{$current_app_virtual_url}">
						&lsaquo;
					</a>
				{/if}
				<a href="{$current_app_virtual_url}{$card->name}" style="color:{$card->color}">
					{$card->name}
				</a>
				</h1>
			</div>
			{if !$logged and $card->is_private}
				<div class="right margintop" style="color:{$card->color}">PRIVATE</div>
			{else}
				{if $card->is_recursive}
					<div class="right margintop" style="color:{$card->color}">{$card->last_edit}</div>
				{else}
					<a class="right margintop" href="{$current_app_virtual_url}{$card->name}/edit">EDIT</a>
				{/if}
			{/if}
			<div class="clear"></div>
		</div>
		<div style="border-color:{$card->color}" class="things">
			{if !$logged and $card->is_private}
				<form id="sawhatlogin"  action="{$current_app_virtual_url}" method="post" enctype="multipart/form-data">
					<input class="input-large" type="password" id="password" name="password" value="" size="20" /><br/>
					<button type="submit" class="btn" name="submit" value="login">Show Card</button>
				</form> 
			{else}
			<p>
			{foreach from=$card->elements item=element}
				{if isset($element->cards)}
					{if count($element->cards) == 3 or count($element->cards) == 2 or count($element->cards) == 1}
						<div class="line darker"><!-- striped -->
						{foreach from=$element->cards item=cardinside}
							<div class="unit size1of{count($element->cards)} ">
							{include file="element.card.tpl" card=$cardinside}
							</div>
						{/foreach}
						</div>
					{/if}
				{else}
					{$element->html}
				{/if}
			{/foreach}
			{/if}
			</p>
		</div>
		{if $card->is_recursive && count($card->files) > 0}
		<div class="border smallpadding">
			<ul>
			{foreach from=$card->files item=file}
				<li><a style="color:{$card->color}" href="{$current_app_virtual_url}{$file->fullname}">{$file->name}</a></li>
			{/foreach}
			</ul>
		</div>
		{/if}
	{/if}
</div>