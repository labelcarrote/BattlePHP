{************************************************

 Card Element 
 
 in :
 - $card : Card
 - $show_banner
 
************************************************}
<div class="sawhat" {if isset($card)}id="{$card->name}"{/if}>
{if !isset($card)}
	<div class="banner auto_clear">
		<div class="left">
			<div>
				<a href="{$batl_current_app_virtual_url}" class="white_text">
				404
				</a>
			</div>
		</div>
	</div>
	<div class="card__content">
		ERROR: included card doesn't exist.
	</div>
{else}
	<style scoped>{$card->style_definition}</style>
	{if !isset($show_banner) || $show_banner}
		{include file="element.card.banner.tpl" card=$card}
	{/if}
	<div class="card__content {if $card->is_light}light{else}dark{/if} auto_clear" data-edit-url="{$batl_current_app_virtual_url}{$card->name}/edit">
	{if !$batl_is_logged and $card->is_private}
		<form id="sawhatlogin" class="form-inline" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">pwd</span>
					<input class="form-control" type="password" id="password" name="password" value="" size="20" />
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-default" name="submit" value="login">Show Card</button>
			</div>
		</form> 
	</div>
	{else}
		{$card->html}
		<!-- WTF is this ending div? -->
	</div>
	{if count($card->files) > 0}
	<div class="smallpadding files marginbottom darker">
		<div class="image_preview hidden">
			<img alt="image preview" src="" class="verticaly_centered" />
		</div>
		<ul>
		{foreach from=$card->files item=file}
			<li><a href="{$batl_root_url}{$file->fullname}" class="{$file->type}_link">{$file->name}</a></li>
		{/foreach}
		</ul>
	</div>
	{/if}
	{/if}
{/if}
</div>