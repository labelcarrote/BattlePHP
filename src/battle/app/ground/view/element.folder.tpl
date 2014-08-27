<div class="padding marginbottom darkerbg folder_title">
	<a class="parent_folder {if $path === ActionGround::ROOT}root_folder{/if}" href="#"><h3 class="current_folder" data-path="{$path}">/{$path}</h3></a>
</div>
{assign var="index" value=0}
{if isset($folders)}
{foreach from=$folders item=folder}
<div class="dir">
	<div class="img_container">
		<img class="dir" data-index="{$index}" data-path="{$folder->fullname}" src="{$batl_current_app_url}public/images/folder.png" alt="{$folder->fullname}" />
		{assign var="index" value=$index+1}
	</div>
	<div class="dir_title marginleft">{$folder->name}</div>
</div>
{/foreach}
{/if}
{if isset($other_images)}
{foreach from=$other_images item=image}
<div class="img_container left">
	<img data-index="{$index}" src="{$image->fullname}" alt="{$image->name}" />
	{assign var="index" value=$index+1}
</div>
{/foreach}
{/if}
{if isset($bg_images)}
{foreach from=$bg_images item=image}
<div class="img_container left">
	<img class="bg" data-index="{$index}" src="{$image->fullname}" alt="{$image->name}" />
	{assign var="index" value=$index+1}
</div>
{/foreach}
{/if}
<div class="clear"></div>