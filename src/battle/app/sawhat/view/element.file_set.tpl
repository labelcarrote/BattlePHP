<div class="image_preview hidden">
	<img alt="image preview" src="" class="verticaly_centered" />
</div>
<ul>
	{foreach from=$card->files item=file}
	<li>
		<a href="{$root_url}{$file->fullname}" title="{$file->name}" class="image_link left block">@{$file->name}</a>
		<span class="image_size left block">{$file->human_readable_size}</span>
		<div class="clearer_left"></div>
	</li>
	{/foreach}
</ul>