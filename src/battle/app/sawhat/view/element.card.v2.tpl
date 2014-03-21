<div class="sawhat">
	{if !isset($card)}
	<div class="banner">
		<div class="left">
			<div>
				<a href="{$current_app_virtual_url}" class="white_text">
				404
				</a>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="things">
		<div>
			ERROR: included card doesn't exist.
			<div class="clearer"></div>
		</div>
	</div>
	{else}
	{include file="element.card.banner.tpl" card=$card}
	<div style="border-color:{$card->color};" class="things">
		{if !$logged and $card->is_private}
		<form id="sawhatlogin" method="post" enctype="multipart/form-data">
			<input class="input-large" type="password" id="password" name="password" value="" size="20" /><br/>
			<button type="submit" class="btn" name="submit" value="login">Show Card</button>
		</form> 
		{else}
<div>
{foreach from=$card->elements item=element}
{if isset($element->cards) && count($element->cards) > 0}
<div class="column_container">
{foreach from=$element->cards item=cardinside}
<div class="unit size1of{count($element->cards)}">
<div class="darker include">
{include file="element.card.v2.tpl" card=$cardinside}
</div>
</div>
{/foreach}
</div>
{else}
{$element->html}
{/if}
{/foreach}
<div class="clearer"></div>
</div>
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
		
	{/if}
</div>