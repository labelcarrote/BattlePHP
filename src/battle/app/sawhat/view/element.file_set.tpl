<div class="image_preview hidden">
	<img alt="image preview" src="" class="verticaly_centered" />
</div>
<ul>
	{foreach from=$card->files item=file}
	<li class="auto_clear">
		<a href="{$batl_root_url}{$file->fullname}" title="{$file->name}" class="image_link left block">@{$file->name}</a>
		<span class="image_size left block">{$file->human_readable_size}</span>
	</li>
	{/foreach}
</ul>