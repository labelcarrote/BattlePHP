{if isset($breadcrumb)}
	<div class="breadcrumb">...
	{foreach from=$breadcrumb item=breadcrumb_item}
		<a
			href="{$breadcrumb_item['url']}"
			{if $breadcrumb_item@iteration == count($breadcrumb)}class="last"{/if}
			{if isset($card) && $breadcrumb_item@iteration == count($breadcrumb)}style="color:{$card->color};"{/if}
		>
			{$breadcrumb_item['name']}
		</a>
		{if $breadcrumb_item@iteration < count($breadcrumb)}
			<span class="bigger">&rsaquo;</span>
		{/if}
	{/foreach}
	</div>
{/if}