<div class="sawhat" {if isset($card)}id="{$card->name}"{/if}>
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
	<style scoped>{$card->style_definition}</style>
	{if !isset($show_banner) || $show_banner}
		{include file="element.card.banner.tpl" card=$card}
	{/if}
	<div class="things">
		{if !$logged and $card->is_private}
		<form id="sawhatlogin" method="post" enctype="multipart/form-data">
			<input class="input-large" type="password" id="password" name="password" value="" size="20" /><br/>
			<button type="submit" class="btn" name="submit" value="login">Show Card</button>
		</form> 
		{else}
{$card->html}
<div class="clearer"></div>
		</div>
		{if count($card->files) > 0}
		<div class="border smallpadding files marginbottom">
			<div class="image_preview hidden">
				<img alt="image preview" src="" class="verticaly_centered" />
			</div>
			<ul>
			{foreach from=$card->files item=file}
				<li><a href="{$root_url}{$file->fullname}" class="image_link">{$file->name}</a></li>
			{/foreach}
			</ul>
		</div>
		{/if}
	{/if}
	{/if}
</div>