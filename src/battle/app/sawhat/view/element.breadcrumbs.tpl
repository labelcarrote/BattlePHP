{if isset($breadcrumbs)}
	<div class="breadcrumbs">
		<a href="{$current_app_virtual_url}">{ConfigurationSawhat::SITE_TITLE}</a>
			{if count($breadcrumbs) > 0}
			<span class="bigger">&rsaquo;</span>&nbsp;...&nbsp;<span class="bigger">&rsaquo;</span>
			{foreach from=$breadcrumbs item=breadcrumbs_item}
				<a
					href="{$breadcrumbs_item['url']}"
					{if $breadcrumbs_item@iteration == count($breadcrumbs)}class="last"{/if}
					{if isset($card) && $breadcrumbs_item@iteration == count($breadcrumbs)}style="color:{$card->color};"{/if}
				>
					{$breadcrumbs_item['name']}
				</a>
				{if $breadcrumbs_item@iteration < count($breadcrumbs)}
					<span class="bigger">&rsaquo;</span>
				{/if}
			{/foreach}
			{/if}
	</div>
{/if}