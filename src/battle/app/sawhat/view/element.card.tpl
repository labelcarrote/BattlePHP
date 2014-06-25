<div class="sawhat" {if isset($card)}id="{$card->name}"{/if}>
	{if !isset($card)}
	<div class="banner auto_clear">
		<div class="left">
			<div>
				<a href="{$current_app_virtual_url}" class="white_text">
				404
				</a>
			</div>
		</div>
	</div>
	<div class="things">
		ERROR: included card doesn't exist.
	</div>
	{else}
	<style scoped>{$card->style_definition}</style>
	{if !isset($show_banner) || $show_banner}
		{include file="element.card.banner.tpl" card=$card}
	{/if}
	<div class="things {if $card->is_light}light{else}dark{/if} auto_clear">
		{if !$logged and $card->is_private}
		<form id="sawhatlogin" class="form-inline" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<div class="input-group" style="width: 300px;">
					<span class="input-group-addon">pwd</span>
					<input class="form-control" type="password" id="password" name="password" value="" size="20" />
				</div>
			</div>
			<button type="submit" class="btn btn-default" name="submit" value="login">Show Card</button>
		</form> 
		{else}
{$card->html}
		</div>
		{if count($card->files) > 0}
		<div class="smallpadding files marginbottom darker">
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